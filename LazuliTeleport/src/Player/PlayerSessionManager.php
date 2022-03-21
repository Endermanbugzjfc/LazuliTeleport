<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Generator;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use SOFe\AwaitGenerator\Await;

class PlayerSessionManager implements Listener
{
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
        Await::f2c(function() use ($event) : Generator {
            $player = $event->getPlayer();
            $arrayKey = "";
            yield from Await::promise(function($then, $catch) use ($player, &$arrayKey) {
                $session = new PlayerSession($player, $then);
                $arrayKey = $session->arrayKey();
                $this->playerSessions[$arrayKey] = $session;
            });
            unset($this->playerSessions[$arrayKey]);
        });
    }
}