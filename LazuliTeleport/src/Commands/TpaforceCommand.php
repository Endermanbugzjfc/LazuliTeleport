<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use CortexPE\Commando\args\IntegerArgument;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Generator;
use pocketmine\command\CommandSender;

class TpaforceCommand extends BaseCommand
{
    public const WAIT_DURATION = "Teleportation wait duration";

    protected function prepare() : void
    {
        $this->registerArgument(0, new IntegerArgument(
            self::WAIT_DURATION,
            true
        ));
    }

    protected function asyncRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : Generator {
        $session = $this->playerSession($sender);
        $session->setForceMode(!$session->getForceMode());
        $duration = $args[self::WAIT_DURATION] ?? null;
        if (is_int($duration)) {
            $session->setForceModeWaitDuration($duration);
        }

        yield from [];
    }
}