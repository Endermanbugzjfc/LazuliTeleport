<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;

class EventListener implements Listener
{

    /**
     * This listener is for opening player sessions.
     * @priority MONITOR
     */
    public function onPlayerLoginEvent(
        PlayerLoginEvent $event
    ) : void {
        $player = $event->getPlayer();
        $session = new PlayerSession($player);
    }
}