<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use pocketmine\plugin\PluginBase;

class LazuliTeleport extends PluginBase
{
    private static self $instance;

    protected function onLoad() : void
    {
        self::$instance = $this;
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
