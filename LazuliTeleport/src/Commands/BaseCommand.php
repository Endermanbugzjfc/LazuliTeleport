<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\BaseCommand as CommandoBaseCommand;
use Generator;
use pocketmine\command\CommandSender;
use SOFe\AwaitGenerator\Await;

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
            InGameCommandException::class => fn(InGameCommandException $err) => $sender->sendMessage($err->getMessage())
            // TODO: DisposableException::class
        ];
        Await::g2c($this->asyncRun(
            $sender,
            $aliasUsed,
            $args
        ), [], $reject);
    }

    /**
     * @param array<string, BaseArgument> $args
     * @throws InGameCommandException
     * @return Generator<mixed, mixed, mixed, void>
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
        LazuliTeleport::getInstance()->getPlayerSession($sender);
    }
}