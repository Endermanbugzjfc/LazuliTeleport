<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use Exception;
use pocketmine\command\CommandSender;

abstract class TerminateCommandException extends Exception
{
    public function handle(
        CommandSender $sender
    ) : void {
    }
}