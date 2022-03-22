<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands\Tpablock;

use Endermanbugzjfc\LazuliTeleport\Commands\BaseSubCommand;

class BlockSubcommand extends BaseSubCommand
{
    /**
     * @param array<string, scalar|Vector3> $args
     * @throws TerminateCommandException
     */
    abstract protected function asyncRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : Generator;

    public static function getInternalName() : string
    {
        return "block";
    }
}