<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\Utils\Utils;
use Generator;
use pocketmine\player\Player;
use Ramsey\Uuid\UuidInterface;
use SOFe\AwaitGenerator\Channel;

class PlayerSession
{
    public function __construct(
        protected Player $player
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

    protected function close() : void
    {
        $this->resolveTeleportationRequest(true);
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }
}