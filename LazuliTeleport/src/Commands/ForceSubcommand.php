<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;

class ForceSubcommand extends BaseSubCommand
{
    protected function prepare() : void
    {
    }

    /**
     * @param array|array<string,mixed|array<mixed>> $args
     * @phpstan-param array<string,mixed|array<mixed>> $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void
    {
        // TODO: Implement onRun() method.
    }
}