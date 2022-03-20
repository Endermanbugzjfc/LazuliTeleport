<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

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
     * @return Generator<mixed, mixed, mixed, void>
     */
    public function getUnresolvedRequest() : ?Channel
    {
    }

    /**
     * @return Generator<mixed, mixed, mixed, void>
     */
    public function awaitTpahereCoolDown() : Generator
    {
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }
}