<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\Commands\PlayerNotFoundException;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Utils\Utils;

trait PlayerFinderActionTrait
{
    /**
     * @throws PlayerNotFoundException
     */
    final protected function findPlayer(
        ?PlayerSession $session = null,
        string $input
    ) : string {
        if (!$this instanceof PlayerFinderActionInterface) {
            throw new RuntimeException("Command finds player but does not implement" . PlayerFinderActionInterface::class);
        }

        $names = LazuliTeleport::getInstance()->getAllPlayerNames();
        $index = Utils::getStringByPrefix($names, $input);
        if ($index === null) {
            throw PlayerNotFoundException::create($session, $this,$input);
        }
    }
}