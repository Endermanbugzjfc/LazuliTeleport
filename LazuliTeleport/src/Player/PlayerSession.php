<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Closure;
use Endermanbugzjfc\LazuliTeleport\Utils\Utils;
use Generator;
use pocketmine\player\Player;
use Ramsey\Uuid\UuidInterface;
use SOFe\AwaitGenerator\Channel;
use function bin2hex;

class PlayerSession
{
    public function __construct(
        protected Player $player,
        protected Closure $onClose
    ) {
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
        $this->getPlayer()->sendMessage();
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
}