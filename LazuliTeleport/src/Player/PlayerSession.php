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
use SOFe\InfoAPI\AnonInfo;
use SOFe\InfoAPI\DurationInfo;
use SOFe\InfoAPI\Info;
use SOFe\InfoAPI\InfoAPI;
use SOFe\InfoAPI\NumberInfo;
use SOFe\InfoAPI\PlayerInfo;
use SOFe\InfoAPI\StringInfo;
use Throwable;
use Vecnavium\FormsUI\CustomForm;
use Vecnavium\FormsUI\ModalForm;
use function array_diff;
use function array_filter;
use function array_unique;
use function bin2hex;
use function count;
use function explode;
use function implode;
use function in_array;
use function sort;
use function spl_object_id;
use function stripos;
use function time;
use const SORT_STRING;

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
    public function awaitTeleportationRequestToBeResolve() : Generator
    {
        return yield from $this->teleportationRequest?->receive()
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
     * @param string[] &$names
     */
    protected static function playerFinderFilter(
        array &$names
    ) : void {
    }

    /**
     * @param string[] &$names
     */
    protected static function playerFinderSorter(
        array &$names
    ) : void {
        sort($names, SORT_STRING);
    }

    /**
     * @param PlayerFinderActionInterface[] $actions
     * @param string $search Empty (empty string) = all players will be listed.
     * @param string[] $selections
     */
    public function openPlayerFinder(
        array $actions,
        PlayerFinderActionInterface $action,
        string $search = "",
        array $selections = [],
    ) : void {
        Await::f2c(function () use (
            $actions,
            $action,
            $search,
            $selections
        ) : Generator {
            $searchBar = "searchBar";
            $resultEntry = "resultEntry";
            $actionSelector = "actionSelector";
            $forceMode = "forceMode";
            $waitDuration = "waitDuration";
            /**
             * I use loops rather than recursive function calls for user-controlled stuff (a form in this case).
             * Player can just put your server in hell by unstoppably pressing the submit button and cause segmentation fault (core dump) due to stack overflow.
             */
            $noTarget = false;
            $pluginName = LazuliTeleport::getInstance()->getName();
            $player = $this->getPlayer();
            $messages = $this->getMessages();
            $info = $this->getInfo();
            $infoNamespace = "$pluginName.PlayerFinder";
            $names = LazuliTeleport::getInstance()->getAllPlayerNames();

            $title = $messages->playerFinderTitle ?? "";
            $title = InfoAPI::resolve($title, $info);
            $err = $messages->playerFinderNoTargetsSelected;
            $label = $messages->playerFinderLabel ?? "";
            $label = InfoAPI::resolve($label, $info);
            $placeholder = $messages->playerFinderPlaceholder ?? "";
            $placeholder = InfoAPI::resolve($placeholder, $info);
            $resultHeader = $messages->playerFinderSearchResultHeader;
            $entry = $messages->playerFinderSearchResultEntry ?? "";

            while (true) {
                $keywords = explode(" ", $search);
                $keywords = array_unique($keywords);
                $found = [];
                foreach ($names as $name) {
                    foreach ($keywords as $keyword) {
                        $stripos = stripos($name, $keyword);
                        if ($stripos === false) {
                            continue;
                        }
                        $found[] = $name;
                    }
                }
                static::playerFinderFilter($found);
                static::playerFinderSorter($found);
                $resultCount = count($found);
                $searchContext = new class(
                    $infoNamespace,
                    [
                        "ResultCount" => new NumberInfo($resultCount),
                        "SearchInput" => new StringInfo($search),
                    ],
                    [
                        $info
                    ]
                ) extends AnonInfo {
                };
                foreach ($found as $name) {
                    $entryInfo = new class(
                        $infoNamespace,
                        [
                            "ResultPlayer" => $name
                        ],
                        [
                            $searchContext
                        ]
                    ) extends AnonInfo {
                    };
                    $entriesResolved[] = InfoAPI::resolve($entry);
                }
                if ($err !== null) {
                    $err = InfoAPI::resolve($err, $searchContext);
                }
                if ($resultHeader !== null) {
                    $resultHeader = InfoAPI::resolve($resultHeader, $searchContext);
                }
                /**
                 * @var array{Player, array<int|string, scalar>|null}
                 */
                $formResult = yield from Await::promise(function ($then) use (
                    // Arguments:
                    $search,
                    $noTarget,

                    // Caches:
                    $searchContext,
                    $found,
                    $entriesResolved,

                    // Message caches:
                    $title,
                    $err,
                    $label,
                    $placeholder,
                    $resultHeader,

                    // Element labels:
                    $searchBar,
                    $resultEntry,
                    $actionSelector,
                    $forceMode,
                    $waitDuration
                ) {
                    $form = new CustomForm($then);
                    $form->setTitle($title);
                    if (
                        $noTarget
                        and
                        $err !== null
                    ) {
                        $form->addLabel($err);
                    }
                    $form->addInput($label, $placeholder, $search, $searchBar);
                    if ($resultHeader !== null) {
                        $form->addLabel($resultHeader);
                    }
                    foreach ($found as $index => $name) {
                        $entryResolved = $entriesResolved[$index]();
                        if (in_array($name, $selections, true)) {
                            $selected = true;
                        } else {
                            $selected = $search === $name;
                        }
                        $form->addToggle($entryResolved, $selected, "$resultEntry.$name");
                    }

                    $player->sendForm($form);
                });
                [, $data] = $formResult;
                if ($data === null) {
                    continue;
                }
                $newSearch = (string)$data[$searchBar];
                if ($search !== $newSearch) {
                    $search = $newSearch;
                    continue;
                }
                $newSelections = [];
                foreach ($data as $k => $v) {
                    $explode = explode(".", (string)$k);
                    if ($explode[0] !== $resultEntry) {
                        continue;
                    }
                    if (!$v) {
                        continue;
                    }
                    $newSelections[] = $explode[1] ?? "";
                }
                if (array_diff($selections, $newSelections) !== []) {
                    $selections = $newSelections;
                    continue;
                }
                if ($selections === []) {
                    $noTarget = true;
                    continue;
                }
                $oldForceMode = $this->getForceMode();
                $oldWaitduration = $this->getForceModeWaitDuration();

                $newForceMode = $data[$forceMode] ?? null;
                if ($newForceMode !== null) {
                    $this->setForceMode((bool)$newForceMode);
                }
                $newWaitDuration = $data[$waitDuration] ?? null;
                if ($newWaitDuration !== null) {
                    $this->setForceModeWaitDuration((int)$newWaitDuration);
                }

                $newActionIndex = (int)$data[$actionSelector];
                $newAction = $actions[$newActionIndex] ?? $action;
                $newAction->runWithSelectedTargets($this, ...$selections);
                break;
            }
        });
    }
}