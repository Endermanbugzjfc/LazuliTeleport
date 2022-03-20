<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use CortexPE\Commando\BaseCommand as CommandoBaseCommand;
use CortexPE\Commando\args\BaseArgument;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Generator;
use SOFe\AwaitGenerator\Await;
use Throwable;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

abstract class BaseCommand extends CommandoBaseCommand
{
    protected function prepare() : void
    {
    }

    /**
     * @param array<string, BaseArgument> $args
     */
    final public function onRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : void {
        $reject = [
            InGameCommandException::class => fn(Throwable $err) => $sender->sendMessage($err->getMessage())
            // TODO: DisposableException::class
        ];
        Await::g2c($this->asyncRun(
            $sender,
            $aliasUsed,
            $args
        ), null, $reject);
    }

    /**
     * @param array<string, BaseArgument> $args
     * @throws InGameCommandException
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
        return LazuliTeleport::getInstance()->getPlayerSession($sender);
    }
}