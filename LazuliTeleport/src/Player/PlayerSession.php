<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Closure;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaforceCommand;
use Endermanbugzjfc\LazuliTeleport\Data\MessageEntry;
use Endermanbugzjfc\LazuliTeleport\Data\Messages;
use Endermanbugzjfc\LazuliTeleport\Data\PermissionDependentOption;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Utils\Utils;
use Generator;
use pocketmine\player\Player;
use poggit\libasynql\DataConnector;
use RuntimeException;
use SOFe\AwaitGenerator\Await;
use SOFe\AwaitGenerator\Channel;
use SOFe\AwaitGenerator\Loading;
use SOFe\InfoAPI\DurationInfo;
use SOFe\InfoAPI\Info;
use SOFe\InfoAPI\InfoAPI;
use SOFe\InfoAPI\NumberInfo;
use SOFe\InfoAPI\PlayerInfo;
use Throwable;
use Vecnavium\FormsUI\ModalForm;
use function array_filter;
use function bin2hex;
use function implode;
use function in_array;
use function spl_object_id;
use function time;

class PlayerSession
{
    public function __construct(
        protected Player $player,
        protected Closure $onClose
    ) {
        // TODO: Load block list.
        /**
         * Group refers to {@link PermissionDependentOption} here.
         * Groups that do not matchc the player's permission will be omitted.
         * Clone the fallback group or the group with least priority if there is no fallback group. Now this becomes the group instance specifically for this player.
         */
        $fallback = PermissionDependentOption::getDefault();
        $groups = LazuliTeleport::getInstance()->getConfigObject()->getOrderedPermissionDependentOptions();
        $groups = array_filter(
            $groups,
            fn (
                PermissionDependentOption $group,
                int|string $permission
             ) : bool => $player->hasPermission((string)$permission),
            ARRAY_FILTER_USE_BOTH
        );
        foreach ($groups as $permission => $group) {
            $this->inheritedGroupsPermission[] = (string)$permission;
        }
        $return = $groups[""] ?? $fallback;
        $return = clone $return;
        /**
         * Then, loop through all other groups by ascending priority order. And {@link PermissionDependentOption::override()} the the properties in player-specific group with values in those group.
         */
        foreach ($groups as $permission => $group) {
            Utils::override($return, $group);
        }
        $this->specificOptions = $return;
    }

    /**
     * @var string[] Permissions.
     */
    protected array $inheritedGroupsPermission = [];

    protected PermissionDependentOption $specificOptions;

    public function getSpecificOptions() : PermissionDependentOption
    {
        return $this->specificOptions;
    }

    public function setSpecificOptions(
        PermissionDependentOption $specificOptions
    ) : void {
        $this->specificOptions = $specificOptions;
    }

    protected Messages $messages;

    public function getMessages() : Messages
    {
        return $this->messages;
    }

    public function setMessages(
        Messages $messages
    ) : void {
        $this->messages = $messages;
    }

    private function connector() : DataConnector
    {
        return LazuliTeleport::getInstance()->getDataConnector();
    }

    public function addBlockedPlayer(
        string $name,
        ?callable $then = null,
        ?callable $catch = null
    ) : void {
        $connector = $this->connector();
        $connector->executeChange("block_list.add", [
            "player" => $this->dbKey(),
            "target" => $name
        ], $then, $catch
        );
    }

    public function removeBlockedPlayerByName(
        string $name,
        ?callable $then = null,
        ?callable $catch = null
    ) : void {
        $connector = $this->connector();
        $connector->executeChange("block_list.remove", [
            "player" => $this->dbKey(),
            "target" => $name
        ], $then, $catch);
    }

    /**
     * @var Loading<string[]>
     */
    protected Loading $blockedPlayers;

    /**
     * @return Generator<mixed, mixed, mixed, string[]> Player names. Notice that the text case of name might not be exact.
     */
    public function getBlockedPlayers() : Generator
    {
        return yield from $this->blockedPlayers->get();
    }

    /**
     * @return Generator<mixed, mixed, mixed, bool>
     */
    public function isNameBlocked(
        string $name
    ) : Generator {
        $blocked = yield from $this->getBlockedPlayers();
        return in_array($name, $blocked, true);
    }

    /**
     * @throws NoTeleportationRequestException
     */
    public function resolveTeleportationRequest(
        bool $accept
    ) : void {
        $request = $this->teleportationRequest;
        Utils::closeChannel($request, $accept)
            ?? throw new NoTeleportationRequestException("Try to resolve a teleportation request when there is no");
    }

    /**
     * @var Channel<bool>|null
     */
    protected ?Channel $teleportationRequest;

    /**
     * @return Generator<mixed, mixed, mixed, bool|null> Null = there is no teleportation request. True = accepted.
     */
    public function awaitTeleportationRequestToBeResolved() : Generator
    {
        return yield from $this->teleportationRequest?->receive()
            ?? [];
    }

    /**
     * @throws TooManyTeleportationRequestException
     */
    public function newTeleportationRequestToBeResolved() : void
    {
        if ($this->teleportationRequest !== null) {
            throw new TooManyTeleportationRequestException("Try to create a new teleportation request when there is already one");
        }
        $this->teleportationRequest = new Channel();
    }

    /**
     * @var int|null Unix timestamp.
     */
    protected ?int $lastTpaUseTime = null;

    public function setLastTpaUseTime(
        ?int $time = null
    ) : void {
        $this->lastTpaUseTime = $time;
    }

    public function getLastTpaUseTime() : ?int
    {
        return $this->lastTpaUseTime;
    }

    /**
     * @var int|null Unix timestamp.
     */
    protected ?int $lastTpahereUseTime = null;

    public function setLastTpahereUseTime(
        ?int $time = null
    ) : void {
        $this->lastTpahereUseTime = $time;
    }

    public function getLastTpahereUseTime() : ?int
    {
        return $this->lastTpahereUseTime;
    }

    public function getTpaCoolDown() : float
    {
        $options = $this->getSpecificOptions();
        $duration = $options->tpaCoolDown ?? time();
        return time() - (float)$duration;
    }

    public function getTpahereCoolDown() : float
    {
        $options = $this->getSpecificOptions();
        $duration = $options->tpahereCoolDown ?? time();
        return time() - (float)$duration;
    }

    protected bool $forceMode = false;

    public function setForceMode(
        bool $forceMode,
    ) : void {
        $this->forceMode = $forceMode;
    }

    public function getForceMode() : bool
    {
        return $this->forceMode;
    }

    protected int $forceModeWaitDuration = 0;

    public function getForceModeWaitDuration() : int
    {
        return $this->forceModeWaitDuration;
    }

    public function setForceModeWaitDuration(
        int $forceModeWaitDuration
    ) : void {
        $this->forceModeWaitDuration = $forceModeWaitDuration;
    }

    /**
     * @return string Player's unique ID in 16 bytes.
     */
    public function arrayKey() : string
    {
        return self::staticArrayKey($this->getPlayer());
    }

    public static function staticArrayKey(
        Player $player
    ) : string {
        return $player->getUniqueId()->getBytes();
    }

    protected function dbKey() : string
    {
        $arrayKey = $this->arrayKey();
        return bin2hex($arrayKey);
    }

    protected function close() : void
    {
        if ($this->teleportationRequest !== null) {
            $this->resolveTeleportationRequest(false);
        }
        ($this->onClose)();
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }

    /**
     * @param callable(bool $data) : bool|null $formCallback Calls right after player submits the form, before any other messages are sent.
     * @param int[] $trace Recursion guard for {@link MessageEntry::$messageOnReject}. Holds SPL object IDs.
     */
    public function displayMessage(
        ?MessageEntry $message,
        ?Info $info = null,
        ?callable $formCallback = null,
        array $trace = []
    ) : void {
        Await::f2c(function () use (
            $message,
            $formCallback,
            $trace,
            $info
        ) : Generator {
            if ($message === null) {
                return;
            }
            $player = $this->getPlayer();
            $info ??= $this->getInfo();

            $formTitle = InfoAPI::resolve($message->formTitle, $info);
            ;
            $formBody = InfoAPI::resolve($message->formBody, $info);
            $acceptButton = $message->acceptButton;
            if ($acceptButton !== null) {
                InfoAPI::resolve($acceptButton, $info);
            }
            $rejectButton = $message->rejectButton;
            if ($rejectButton !== null) {
                $rejectButton = InfoAPI::resolve($rejectButton, $info);
            }
            if (
                $formTitle !== ""
                or
                $formBody !== ""
            ) {
                /**
                 * @var array{Player, bool}
                 */
                $formResult = yield from Await::promise(function ($then) use (
                    $player
                ) {
                    $form = new ModalForm($then);
                    $player->sendForm($form);
                });
                [, $data] = $formResult;
                if ($formCallback !== null) {
                    $ok = $formCallback($data);
                }
                if ($ok ?? $data) {
                    return;
                }
                $messageOnReject = $message->messageOnReject;
                if ($messageOnReject !== null) {
                    $id = spl_object_id($messageOnReject);
                    if (in_array($id, $trace, true)) {
                        $permissionsList = implode(", ", $this->inheritedGroupsPermission);
                        $err = new RuntimeException("Recursive message detected, the problemetic config is among $permissionsList");
                        $this->error($err);
                        return;
                    }
                    return;
                }
            }

            $chat = InfoAPI::resolve($message->chat, $info);
            if ($chat !== "") {
                $player->sendMessage($chat);
            }

            $title = InfoAPI::resolve($message->title, $info);
            $subtitle = InfoAPI::resolve($message->subtitle, $info);
            $actionbar = InfoAPI::resolve($message->actionbar, $info);
            if (
                $title !== ""
                or
                $subtitle !== ""
            ) {
                $fadeIn = $message->fadeIn = -1;
                $stay = $message->stay = -1;
                $fadeOut = $message->fadeOut = -1;
                $player->sendTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
            }

            $actionbar = InfoAPI::resolve($message->actionbar, $info);
            if ($actionbar !== "") {
                $player->sendActionBarMessage($actionbar);
            }
        });
    }

    protected bool $errorRecursionLock = false;

    public function error(Throwable $err) : void
    {
        if ($this->errorRecursionLock) {
            $this->errorRecursionLock = false;
            return;
        }
        $this->errorRecursionLock = true;

        $logger = LazuliTeleport::getInstance()->getLogger();
        $logger->logException($err);
        $message = $this->getMessages()->internalServerError;
        $this->displayMessage($message);

        $this->errorRecursionLock = false;
    }

    /**
     * @return PlayerSessionInfo Do NOT reuse the same info instance.
     */
    public function getInfo() : PlayerSessionInfo
    {
        $options = $this->getSpecificOptions();
        return new PlayerSessionInfo(
            new NumberInfo(1.0),
            new DurationInfo((float)$options->tpaCoolDown),
            new DurationInfo($this->getTpaCoolDown()),
            new NumberInfo((float)$options->tpahereRequesteeLimit),
            new DurationInfo($this->getTpahereCoolDown()),
            new DurationInfo((float)$options->tpahereCoolDown),
            new DurationInfo((float)$options->waitDurationAfterAcceptRequest),
            new DurationInfo((float)$this->getForceModeWaitDuration()),
            new PlayerInfo($this->getPlayer())
        );
    }

    /**
     * @return Generator<mixed, mixed, mixed, bool>
     */
    public function hasForceModePermission() : Generator
    {
        $tpaforce = yield from TpaforceCommand::getInstance();
        $permission = $tpaforce->getPermission();
        if ($permission === null) {
            return true;
        }
        return $this->getPlayer()->hasPermission($permission);
    }

    /**
     * @return Generator<mixed, mixed, mixed, bool>
     */
    public function hasOfflineRequestPermission() : Generator
    {
        yield from [];

        $options = $this->getSpecificOptions();
        return $options->allowOfflineRequests ?? false;
    }

    /**
     * @param callable(string &$names) : void $filter
     * @param string $search Empty (empty string) = all players will be listed.
     * @param string[] $selections
     * @param PlayerFinderActionInterface[] $actions
     */
    public function openPlayerFinder(
        ?PlayerFinderActionInterface $action = null,
        string $search = "",
        array $selections = [],
        ?callable $filter = null,
        ?array $actions = null,
    ) : void {
        PlayerFinder::open(
            $this,
            $action,
            $search,
            $selections,
            $filter,
            $actions
        );
    }
}