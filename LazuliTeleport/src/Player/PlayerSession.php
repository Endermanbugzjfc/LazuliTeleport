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
use SOFe\AwaitGenerator\Channel;
use Vecnavium\FormsUI\ModalForm;
use function array_filter;
use function bin2hex;

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
     * @return Generator<mixed, mixed, mixed, void>
     */
    public function awaitTpahereCoolDown() : Generator
    {
    }

    protected bool $forceMode = false;

    public function setForceMode(
        bool $forceMode,
        bool $sendMessage = true
    ) : void {
        $this->forceMode = $forceMode;
        $messages = $this->getMessages();
        $message = $forceMode
            ? $messages->forceModeEnabled
            : $messages->forceModeDisabled;
        $this->displayMessage($message);
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
     * @return Channel<null>|null
     */
    public function getTeleportationRequest()
    {
        return $this->teleportationRequest;
    }

    /**
     * @param Channel<null>|null $teleportationRequest
     *
     * @return self
     */
    public function setTeleportationRequest($teleportationRequest)
    {
        $this->teleportationRequest = $teleportationRequest;

        return $this;
    }

    public function displayMessage(
        ?MessageEntry $message,
        ?callable $formCallback = null
    ) : void {
        if ($message === null) {
            return;
        }
        $player = $this->getPlayer();

        $chat = $message->chat;
        if ($chat !== "") {
            $player->sendMessage($chat);
        }

        $title = $message->title;
        $subtitle = $message->subtitle;
        $actionbar = $message->actionbar;
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

        $actionbar = $message->actionbar;
        if ($actionbar !== "") {
            $player->sendActionBarMessage($actionbar);
        }

        $formTitle = $message->formTitle;
        $formBody = $message->formBody;
        $acceptButton = $message->acceptButton;
        $rejectButton = $message->rejectButton;
        if (
            $formTitle !== ""
            or
            $formBody !== ""
        ) {
            $formCallback ??= fn() => null;
            $form = new ModalForm($formCallback);
            $player->sendForm($form);
        }
    }
}