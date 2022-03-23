<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport;

use CortexPE\Commando\PacketHooker;
use Endermanbugzjfc\ConfigStruct\Emit;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\LazuliTeleport\Commands\BaseCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\Tpablock\BlockSubcommand;
use Endermanbugzjfc\LazuliTeleport\Commands\Tpablock\ListSubcommand;
use Endermanbugzjfc\LazuliTeleport\Commands\Tpablock\TpablockCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\Tpablock\UnblockSubcommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpacceptCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaforceCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpahereCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TparejectCommand;
use Endermanbugzjfc\LazuliTeleport\Data\Commands;
use Endermanbugzjfc\LazuliTeleport\Data\Messages;
use Endermanbugzjfc\LazuliTeleport\Data\PluginConfig;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSessionInfo;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSessionManager;
use Endermanbugzjfc\LazuliTeleport\Player\TeleportationRequestContextInfo;
use Endermanbugzjfc\LazuliTeleport\Utils\SingletonsHolder;
use Endermanbugzjfc\LazuliTeleport\Utils\Utils;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use RuntimeException;
use function array_map;
use function count;
use function explode;
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

        $playerSessionManager = new PlayerSessionManager();
        $this->getServer()->getPluginManager()->registerEvents($playerSessionManager, $this);
        $this->playerSessionManager = $playerSessionManager;
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
                $explode = explode(".", $internalName);
                $internalProfileName = $explode[count($explode) - 1];
                $default = $defaults->$internalProfileName;
                $profile = $commandProfiles->$internalProfileName ?? null;
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
                TpaforceCommand::class,

                TpablockCommand::class,
                ListSubcommand::class,
                BlockSubcommand::class,
                UnblockSubcommand::class
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
        unset($this->playerManager);
    }

    protected PlayerSessionManager $playerSessionManager;

    public function getPlayerSession(
        Player $player
    ) : PlayerSession {
        $arrayKey = PlayerSession::staticArrayKey($player);
        return $this->playerSessionManager->playerSessions[$arrayKey];
    }

    /**
     * Get all player names on the server, no matter the player is online or offline. Notice that the text case of name might not be exact.
     * @return string[]
     */
    public function getAllPlayerNames() : array
    {
        return $this->playerSessionManager->allPlayerNames;
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
