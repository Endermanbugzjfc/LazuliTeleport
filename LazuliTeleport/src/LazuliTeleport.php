<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Data\PluginConfig;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
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
        $description = "LazuliTeleport wait time permission";
        foreach ($config->waitTimeAfterAcceptRequest as $permission => $time) {
            $permissionInstance = new Permission($permission, $description);
            PermissionManager::getInstance()->addPermission($permissionInstance);
        }
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
