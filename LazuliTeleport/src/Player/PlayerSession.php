<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Closure;
use Endermanbugzjfc\LazuliTeleport\Data\MessageEntry;
use Endermanbugzjfc\LazuliTeleport\Data\Messages;
use Endermanbugzjfc\LazuliTeleport\Data\PermissionDependentOption;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Utils\Utils;
use Generator;
use pocketmine\player\Player;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;
use SOFe\AwaitGenerator\Await;
use SOFe\AwaitGenerator\Channel;
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

    /**
     * @return Generator<mixed, mixed, mixed, void>
     */
    public function addBlockedPlayer(
        UuidInterface $uuid,
        string $name
    ) : Generator {
    }

    /**
     * @return Generator<mixed, mixed, mixed, void>
     */
    public function removeBlockedPlayerByUniqueId(
        UuidInterface $uuid
    ) : Generator {
    }

    /**
     * @return Generator<mixed, mixed, mixed, void>
     */
    public function removeBlockedPlayerByName(
        string $name
    ) : Generator {
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string> Key = player unique ID string in 16 bytes. Value = player name.
     */
    public function getBlockedPlayers() : array
    {
    }

    /**
     * @throws NoTeleportationRequestException
     */
    public function resolveTeleportationRequest(
        bool $silent = false
    ) : void {
        $request = $this->teleportationRequest;
        Utils::closeChannel($request)
            ?? throw new NoTeleportationRequestException("Try to resolve a teleportation request when there is no");
    }

    /**
     * @var Channel<null>|null
     */
    protected ?Channel $teleportationRequest;

    /**
     * @return Generator<mixed, mixed, mixed, void>
     */
    public function awaitTeleportationRequestToBeResolve() : Generator
    {
        yield from $this->teleportationRequest?->receive()
            ?? [];
    }

    /**
     * @throws TooManyTeleportationRequestException
     */
    public function newTeleportationRequestToBeResolve() : void
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

    /**
     * @return Generator<mixed, mixed, mixed, void>
     */
    public function awaitTpahereCoolDown() : Generator
    {
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

    public function arrayKey() : string
    {
        return $this->getPlayer()->getUniqueId()->getBytes();
    }

    protected function dbKey() : string
    {
        $arrayKey = $this->arrayKey();
        return bin2hex($arrayKey);
    }

    protected function close() : void
    {
        $this->resolveTeleportationRequest(true);
        ($this->onClose)();
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }

    /**
     * @param callable(bool $data) : void|null $formCallback Calls right after player submits the form, before any other messages are sent.
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
                    $formCallback($data);
                }
                $messageOnReject = $message->messageOnReject;
                if ($messageOnReject !== null) {
                    $id = spl_object_id($messageOnReject);
                    if (in_array(
                        $id,
                        $trace,
                        true
                    )) {
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
        $tpaDefaultCoolDown = (float)$options->tpaCoolDown;
        $tpahereDefaultCoolDown = (float)$options->tpahereCoolDown;
        $tpaLastUse = $this->getLastTpaUseTime() ?? time();
        $tpahereLastUse = $this->getLastTpahereUseTime() ?? time();
        $tpaCoolDown = time() - $tpaDefaultCoolDown;
        $tpahereCoolDown = time() - $tpahereDefaultCoolDown;
        return new PlayerSessionInfo(
            new NumberInfo(1.0),
            new DurationInfo($tpaCoolDown),
            new DurationInfo($tpaDefaultCoolDown),
            new NumberInfo((float)$options->tpahereRequesteeLimit),
            new DurationInfo($tpahereCoolDown),
            new DurationInfo($tpahereDefaultCoolDown),
            new DurationInfo((float)$options->waitDurationAfterAcceptRequest),
            new DurationInfo((float)$this->getForceModeWaitDuration()),
            new PlayerInfo($this->getPlayer())
        );
    }
}