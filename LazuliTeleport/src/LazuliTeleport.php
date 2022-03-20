<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use Endermanbugzjfc\ConfigStruct\Emit;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Data\Messages;
use Endermanbugzjfc\LazuliTeleport\Data\PluginConfig;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use RuntimeException;
use function file_exists;
use function file_put_contents;

class LazuliTeleport extends PluginBase
{
    private static self $instance;

    protected function onLoad() : void
    {
        self::$instance = $this;
    }

    protected PluginConfig $configObject;

    public function getConfigObject() : PluginConfig
    {
        return $this->configObject;
    }


    public function setConfigObject(
        PluginConfig $configObject
    ) : void {
        $this->configObject = $configObject;
    }

    public function getConfig() : Config
    {
        throw new RuntimeException("Use getConfigObject() instead");
    }

    protected Messages $messages;

    public function getMessages() : Messages
    {
        return $this->messages;
    }

    public function setMessages(Messages $messages) : void
    {
        $this->messages = $messages;
    }

    protected function onEnable() : void
    {
        $this->configObject = new PluginConfig();
        $path = $this->getDataFolder() . "config.yml";
        if (!file_exists($path)) {
            $data = Emit::object($this->configObject);
            file_put_contents($path, $data);
        } else {
            $data = (new Config($path))->getAll();
            $context = Parse::object($this->configObject, $data);
            $context->copyToObject($this->configObject, $path);
        }
        $this->messages = new Messages();
        $messagesPath = $this->getDataFolder() . "messages.yml";
        if (!file_exists($messagesPath)) {
            $data = Emit::object($this->messages);
            file_put_contents($path, $data);
        } else {
            $messagesData = (new Config($messagesPath))->getAll();
            $context = Parse::object($this->messages, $messagesData);
            $context->copyToObject($this->messages, $messagesPath);
        }

        $pluginName = $this->getName();
        $waitDurationDescription = $pluginName . " wait duration permission";
        foreach ($this->configObject->waitDurationAfterAcceptRequest as $permission => $time) {
            $permissionInstances[] = new Permission($permission, $waitDurationDescription);
        }

        $requesteeLimitDescription = $pluginName . " tpahere requestee limit permission";
        foreach ($this->configObject->tpahereRequesteeLimit as $permission => $limit) {
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
