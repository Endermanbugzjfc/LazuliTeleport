<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use AssertionError;
use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\BaseCommand as CommandoBaseCommand;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Generator;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;
use Throwable;

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
            TerminateCommandException::class => function (Throwable $err) use ($sender) : void {
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
     * @param array<string, BaseArgument> $args
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
        return LazuliTeleport::getInstance()->getPlayerSession($sender);
    }
}