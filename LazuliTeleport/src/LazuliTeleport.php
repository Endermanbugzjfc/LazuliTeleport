<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use AssertionError;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\PacketHooker;
use Endermanbugzjfc\ConfigStruct\Emit;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Commands\InGameCommandException;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpablockCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpacceptCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaforceCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpahereCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TparejectCommand;
use Endermanbugzjfc\LazuliTeleport\Data\CommandProfile;
use Endermanbugzjfc\LazuliTeleport\Data\Messages;
use Endermanbugzjfc\LazuliTeleport\Data\PermissionDependentOption;
use Endermanbugzjfc\LazuliTeleport\Data\PluginConfig;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use RuntimeException;
use const ARRAY_FILTER_USE_BOTH;
use function array_filter;
use function file_exists;
use function file_put_contents;
use function strtolower;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
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
        $description = $pluginName . " permission dependent option group";
        foreach ($this->configObject->permissionDependentOptions as $permission => $group) {
            $permissionInstance = new Permission((string)$permission, $description);
            PermissionManager::getInstance()->addPermission($permissionInstance);
        }

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $commands = [
            $this->createCommandFromProfile(
                TpaCommand::class,
                "tpa"
            ),
            $this->createCommandFromProfile(
                TpahereCommand::class,
                "tpahere"
            ),
            $this->createCommandFromProfile(
                TpacceptCommand::class,
                "tpaccept"
            ),
            $this->createCommandFromProfile(
                TparejectCommand::class,
                "tpareject"
            ),
            $this->createCommandFromProfile(
                TpablockCommand::class,
                "tpablock"
            ),
            $this->createCommandFromProfile(
                TpaforceCommand::class,
                "tpaforce"
            )
        ];
        $this->getServer()->getCommandMap()->registerAll($pluginName, $commands);
    }

    /**
     * Default values will be used if the user-defined profile does not have it.
     * @template T of BaseCommand
     * @phpstan-param class-string<T> $class
     * @param string $name Command name to retrieve the profile and generate the permission.
     * @return T
     */
    protected function createCommandFromProfile(
        string $class,
        string $name
    ) : BaseCommand {
        $defaults = PluginConfig::getDefaultCommandProfiles();
        $default = $defaults[$name];
        $profile = $this->getConfigObject()->commands[$name]
            ?? null;
        if ($profile !== null) {
            $profile = new CommandProfile();
            $default->name ??= $profile->name;
            $default->description ??= $profile->description;
            $default->aliases ??= $profile->aliases;
        } else {
            $profile = $default;
        }

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

    public function getPlayerOptions(
        Player $player
    ) : PermissionDependentOption {
        $fallback = PermissionDependentOption::getDefault();
        $groups = $this->getConfigObject()->getOrderedPermissionDependentOptions();
        $groups = array_filter(
            $groups,
            fn (
                PermissionDependentOption $group,
                string $permission
             ) : bool => $player->hasPermission((string)$permission),
            ARRAY_FILTER_USE_BOTH
        );
        $return = $groups[""] ?? $fallback;
        foreach ($groups as $permission => $group) {
            $v = $group->waitDurationAfterAcceptRequest;
            if ($v !== null) {
                $return->waitDurationAfterAcceptRequest = $v;
            }
            $v = $group->tpaCoolDown;
            if ($v !== null) {
                $return->waitDurationAfterAcceptRequest = $v;
            }
            $v = $group->tpahereRequesteeLimit;
            if ($v !== null) {
                $return->waitDurationAfterAcceptRequest = $v;
            }
            $v = $group->tpahereCoolDown;
            if ($v !== null) {
                $return->waitDurationAfterAcceptRequest = $v;
            }
        }

        return $return;
    }

    public function getPlayerSession(
        Player $player
    ) : PlayerSession {
        try {
            return $this->playerSession($player);
        } catch (InGameCommandException) {
            throw new AssertionError("unreachable");
        }
    }

    /**
     * @throws InGameCommandException
     */
    public static function playerSession(
        CommandSender $sender
    ) : PlayerSession {
        if (!$sender instanceof Player) {
            throw new InGameCommandException("This command must be executed in-game");
        }
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
