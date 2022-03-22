<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands\Tpablock;

use Endermanbugzjfc\LazuliTeleport\Commands\BaseSubCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TerminateCommandException;
use Generator;
use pocketmine\command\CommandSender;

class UnblockSubcommand extends BaseSubCommand
{
    protected function asyncRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : Generator {
        yield from [];
    }

    public static function getInternalName() : string
    {
        return "tpablock.unblock";
    }
}