<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use AssertionError;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Generator;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;
use Throwable;
use function strtolower;

trait BaseCommandTrait
{
    final protected function prepare() : void
    {
        LazuliTeleport::getInstance()->getSingletonsHolder()->register($this);
        $pluginName = LazuliTeleport::getInstance()->getName();
        $lowerPluginName = strtolower($pluginName);
        $internalName = $this->getInternalName();
        $this->setPermission("$lowerPluginName.$internalName");
        Await::g2c($this->asyncPrepare());
    }

    protected function asyncPrepare() : Generator
    {
    }

    /**
     * @return Generator<mixed, mixed, mixed, static>
     */
    public static function getInstance() : Generator
    {
        return yield from LazuliTeleport::getInstance()->getSingletonsHolder()->waitFor(static::class);
    }

    /**
     * @param array<string, scalar|Vector3> $args
     */
    final public function onRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : void {
        $reject = [
            TerminateCommandException::class => function (Throwable $err) use (
                $sender
            ) : void {
                if (!$err instanceof TerminateCommandException) {
                    throw new AssertionError("unreachable");
                }
                $err->handle($sender);
            }
            // TODO: DisposableException::class
        ];
        Await::g2c($this->asyncRun(
            $sender,
            $aliasUsed,
            $args
        ), null, $reject);
    }

    /**
     * @param array<string, scalar|Vector3> $args
     * @throws TerminateCommandException
     */
    abstract protected function asyncRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : Generator;

    /**
     * @throws InGameCommandException
     */
    final protected function playerSession(
        CommandSender $sender
    ) : PlayerSession {
        if (!$sender instanceof Player) {
            throw new InGameCommandException("This command must be executed in-game");
        }
        $session = LazuliTeleport::getInstance()->getPlayerSession($sender);
        $force = TpaforceCommand::getInstance();
        if (
            $session->getForceMode()
            and
            !$sender->hasPermission((string)$force->getPermission())
        ) {
            $session->setForceMode(false);
        }

        return $session;
    }

    abstract public static function getInternalName() : string;
}