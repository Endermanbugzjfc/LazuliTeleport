<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use Generator;
use pocketmine\command\CommandSender;

class TpaCommand extends BaseCommand
{
    /**
     * @param array|array<string,mixed|array<mixed>> $args
     * @phpstan-param array<string,mixed|array<mixed>> $args
     */
    protected function asyncRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : Generator {
        // TODO: Implement onRun() method.
    }

    public static function getInternalName() : string
    {
        return "tpa";
    }
}