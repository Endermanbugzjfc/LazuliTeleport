<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Data\PluginConfig;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class LazuliTeleport extends PluginBase
{
    private static self $instance;

    protected function onLoad() : void
    {
        self::$instance = $this;
    }

    protected function onEnable() : void
    {
        $path = $this->getDataFolder() . "config.yml";
        $data = (new Config($path))->getAll();
        $config = new PluginConfig();
        $context = Parse::object($config, $data);
        $context->copyToObject($config, $path);
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
