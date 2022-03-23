<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Generator;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Server;
use SOFe\AwaitGenerator\Await;
use function array_diff;
use function rtrim;
use function scandir;

class PlayerSessionManager implements Listener
{
    /**
     * @var string[]
     */
    public $allPlayerNames = [];

    public function __construct()
    {
        $playersFolder = Server::getInstance()->getDataPath() . "players";
        $scandir = scandir($playersFolder);
        $files = $scandir === false
            ? []
            : array_diff($scandir, [".", ".."]);
        foreach ($files as $file) {
            $name = rtrim($file, ".dat");
            if ($file === $name) {
                continue;
            }
            $this->allPlayerNames[] = $name;
        }
    }

    /**
     * @var PlayerSession[]
     * @phpstan-var array<string, PlayerSession> Key = player's unique ID in 16 bytes.
     */
    public $playerSessions = [];

    /**
     * This listener is for opening player sessions.
     * @priority MONITOR
     */
    public function onPlayerLoginEvent(
        PlayerLoginEvent $event
    ) : void {
        Await::f2c(function () use (
            $event
        ) : Generator {
            $player = $event->getPlayer();
            $arrayKey = "";
            yield from Await::promise(function ($then) use (
                $player,
                &$arrayKey
            ) {
                $session = new PlayerSession($player, $then);
                $arrayKey = $session->arrayKey();
                $this->playerSessions[$arrayKey] = $session;
            });
            unset($this->playerSessions[$arrayKey]);
        });
    }
}