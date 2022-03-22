<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use CortexPE\Commando\PacketHooker;
use Endermanbugzjfc\ConfigStruct\Emit;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Commands\BaseCommand;
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
use Endermanbugzjfc\LazuliTeleport\Utils\Utils;
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
            $commandProfiles
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

        $commands = array_map(
            function (string $class) use (
                $commandProfiles
            ) : BaseCommand {
                /**
                 * @var class-string<BaseCommand> $class
                 */
                $defaults = new Commands();
                $internalName = $class::getInternalName();
                $default = $defaults->$internalName;
                $profile = $commandProfiles->$internalName ?? null;
                if ($profile !== null) {
                    Utils::override($profile, $default);
                } else {
                    $profile = $default;
                }

                return new $class(
                    $this,
                    $profile->name,
                    $profile->description,
                    $profile->aliases
                );
            }, [
                TpaCommand::class,
                TpahereCommand::class,
                TpacceptCommand::class,
                TparejectCommand::class,
                TpablockCommand::class,
                TpaforceCommand::class
            ]
        );

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
    }

    public function getPlayerSession(
        Player $player
    ) : PlayerSession {
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
