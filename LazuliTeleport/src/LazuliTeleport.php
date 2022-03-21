<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\PacketHooker;
use Endermanbugzjfc\ConfigStruct\Emit;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpablockCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpacceptCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaforceCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpahereCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TparejectCommand;
use Endermanbugzjfc\LazuliTeleport\Data\Commands;
use Endermanbugzjfc\LazuliTeleport\Data\Form;
use Endermanbugzjfc\LazuliTeleport\Data\Messages;
use Endermanbugzjfc\LazuliTeleport\Data\PermissionDependentOption;
use Endermanbugzjfc\LazuliTeleport\Data\PluginConfig;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSessionInfo;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSessionManager;
use Endermanbugzjfc\LazuliTeleport\Player\TeleportationRequestContextInfo;
use Endermanbugzjfc\LazuliTeleport\Utils\SingletonsHolder;
use RuntimeException;
use const ARRAY_FILTER_USE_BOTH;
use function array_filter;
use function file_exists;
use function file_put_contents;
use function strtolower;
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

    /**
     * @var Messages[]
     * @phpstan-var array<string, Messages> Key = permission.
     */
    protected $permissionToMessagesMap = [];

    public function setMessagesByPermission(
        string $permission,
        Messages $messages
    ) : void {
        $this->permissionToMessagesMap[$permission] = $messages;
    }

    public function getMessagesByPermission(
        string $permission
    ) : ?Messages {
        return $this->permissionToMessagesMap[$permission]
            ?? null;
    }

    /**
     * @var Form[]
     * @phpstan-var array<string, Form> Key = permission.
     */
    protected $permissionToFormMap = [];

    public function setFormByPermission(
        string $permission,
        Form $form
    ) : void {
        $this->permissionToFormMap[$permission] = $form;
    }

    public function getFormByPermission(
        string $permission
    ) : ?Form {
        return $this->permissionToFormMap[$permission]
            ?? null;
    }

    protected SingletonsHolder $singletonsHolder;

    protected function onEnable() : void
    {
        $this->singletonsHolder = new SingletonsHolder();
        $dataFolder = $this->getDataFolder();
        $files = [
            PluginConfig::class => $dataFolder . "config.yml",
            Commands::class => $dataFolder . "commands.yml",
        ];
        foreach ($files as $class => $path) {
            $object = new $class();
            if (!file_exists($path)) {
                $data = Emit::object($object);
                file_put_contents($path, $data);
            } else {
                $data = (new Config($path))->getAll();
                $context = Parse::object($object, $data);
                $context->copyToObject($object, $path);
            }
            $objects[] = $object;
        }
        /**
         * @var array{PluginConfig, Commands} $objects
         */
        [
            $this->configObject,
            $this->commands
        ] = $objects;

        $pluginName = $this->getName();
        $description = $pluginName . " permission dependent option group";
        foreach ($this->configObject->permissionDependentOptions as $permission => $group) {
            $permissionInstance = new Permission((string)$permission, $description);
            PermissionManager::getInstance()->addPermission($permissionInstance);
        }

        $listener = new PlayerSessionManager();
        $this->getServer()->getPluginManager()->registerEvents($listener, $this);
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

        PlayerSessionInfo::init();
        TeleportationRequestContextInfo::init();
    }

    public function getSingletonsHolder() : SingletonsHolder
    {
        return $this->singletonsHolder;
    }

    protected function onDisable() : void
    {
        unset($this->singletonsHolder);
        unset($this->configObject);
        unset($this->permissionToMessagesMap);
        unset($this->permissionToFormMap);
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
        $defaults = new Commands();
        $default = $defaults->$name;
        $profiles = $this->getCommands();
        $profile = $profiles->$name ?? null;
        if ($profile !== null) {
            $profile->name ??= $default->name;
            $profile->description ??= $default->description;
            $profile->aliases ??= $default->aliases;
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
                int|string $permission
             ) : bool => $player->hasPermission((string)$permission),
            ARRAY_FILTER_USE_BOTH
        );
        $return = $groups[""] ?? $fallback;
        $return = clone $return;
        foreach ($groups as $permission => $group) {
            $return->override($group);
        }

        return $return;
    }

    public function getPlayerSession(
        Player $player
    ) : PlayerSession {
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }

    /**
     * @param mixed $singletonsHolder
     *
     * @return self
     */
    public function setSingletonsHolder($singletonsHolder)
    {
        $this->singletonsHolder = $singletonsHolder;

        return $this;
    }
}
