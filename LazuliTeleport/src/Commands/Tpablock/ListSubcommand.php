<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands\Tpablock;

use Endermanbugzjfc\LazuliTeleport\Commands\BaseSubCommand;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerFinderActionInterface;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Generator;
use pocketmine\command\CommandSender;
use Ramsey\Uuid\UuidInterface;

class ListSubcommand extends BaseSubCommand
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
        return "tpablock.list";
    }
}