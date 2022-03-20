<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

class TpaforceCommand extends BaseCommand
{
    protected function prepare() : void
    {
    }

    /**
     * @param array|array<string,mixed|array<mixed>> $args
     * @phpstan-param array<string,mixed|array<mixed>> $args
     */
    public function onRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : void {
        // TODO: Implement onRun() method.
    }
}