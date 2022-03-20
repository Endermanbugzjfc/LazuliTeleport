<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\PacketHooker;
use Endermanbugzjfc\ConfigStruct\Emit;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpacceptCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpahereCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TparejectCommand;
use Endermanbugzjfc\LazuliTeleport\Data\CommandProfile;
use Endermanbugzjfc\LazuliTeleport\Data\Messages;
use Endermanbugzjfc\LazuliTeleport\Data\PluginConfig;
use RuntimeException;
use function file_exists;
use function file_put_contents;
use function strtolower;
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

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $commands = [
            $this->createCommandFromProfile(
                TpaCommand::class,
                "tpa",
                "Request teleportation to another player"
            ),
            $this->createCommandFromProfile(
                TpahereCommand::class,
                "tpahere",
                "Request teleporting another player to you",
                [
                    "tphere"
                ]
            ),
            $this->createCommandFromProfile(
                TpacceptCommand::class,
                "tpaccept",
                "Accept a teleportation request"
            ),
            $this->createCommandFromProfile(
                TparejectCommand::class,
                "tpareject",
                "Reject a teleportation request",
                [
                    "tpreject",
                    "tpadeny",
                    "tpdeny"
                ]
            ),
        ];
        $this->getServer()->getCommandMap()->registerAll($pluginName, $commands);
    }

    /**
     * Default values will be used if the user-defined profile does not have it.
     * @template T of BaseCommand
     * @phpstan-param class-string<T> $class
     * @param string $name Default command / subcommand name. Also the key of a command profile in plugin config and the second node in command's permission.
     * @param string $description Default description.
     * @param string[] $aliases Default aliases.
     * @return T
     */
    protected function createCommandFromProfile(
        string $class,
        string $name,
        string $description,
        array $aliases = []
    ) : BaseCommand {
        $profile = $this->getConfigObject()->commands[$name]
            ?? new CommandProfile();
        $profile->name ??= $name;
        $profile->description ??= $description;
        $profile->aliases ??= $aliases;

        $command = new $class(
            $this,
            $profile->name,
            $profile->description,
            $profile->aliases
        );
        $lowerPluginName = strtolower($this->getName());
        $command->setPermission("$lowerPluginName.$name");

        return $command;
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
