<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\Utils\Utils;
use Generator;
use Ramsey\Uuid\UuidInterface;
use SOFe\AwaitGenerator\Channel;
use pocketmine\player\Player;

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

    protected function close() : void {
        $requestChannel = $this->getUnresolvedRequest();
        Utils::closeChannel($requestChannel);
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }
}