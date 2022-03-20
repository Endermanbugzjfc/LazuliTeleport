<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use Endermanbugzjfc\ConfigStruct\Emit;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Data\PluginConfig;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use function file_exists;
use function file_put_contents;

class LazuliTeleport extends PluginBase
{
    private static self $instance;

    protected function onLoad() : void
    {
        self::$instance = $this;
    }

    protected function onEnable() : void
    {
        $config = new PluginConfig();
        $path = $this->getDataFolder() . "config.yml";
        if (!file_exists($path)) {
            $data = Emit::object($config);
            file_put_contents($path, $data);
        } else {
            $data = (new Config($path))->getAll();
            $context = Parse::object($config, $data);
            $context->copyToObject($config, $path);
        }

        $pluginName = $this->getName();
        $waitDurationDescription = $pluginName . " wait duration permission";
        foreach ($config->waitDurationAfterAcceptRequest as $permission => $time) {
            $permissionInstances[] = new Permission($permission, $waitDurationDescription);
        }

        $requesteeLimitDescription = $pluginName . " tpahere requestee limit permission";
        foreach ($config->tpahereRequesteeLimit as $permission => $limit) {
            $permissionInstances[] = new Permission($permission, $requesteeLimitDescription);
        }
        foreach ($permissionInstances ?? [] as $permissionInstance) {
            PermissionManager::getInstance()->addPermission($permissionInstance);
        }
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
