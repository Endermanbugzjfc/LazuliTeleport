<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use CortexPE\Commando\args\IntegerArgument;
use Generator;
use pocketmine\command\CommandSender;
use function is_int;

class TpaforceCommand extends BaseCommand
{
    public const WAIT_DURATION = "Teleportation wait duration";

    protected function pre() : void
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

        $duration = $args[self::WAIT_DURATION] ?? null;
        if (is_int($duration)) {
            $session->setForceModeWaitDuration($duration);
        }
        $forceMode = !$session->getForceMode();
        $session->setForceMode($forceMode);

        $messages = $session->getMessages();
        $message = $forceMode
            ? $messages->forceModeEnabled
            : $messages->forceModeDisabled;
        $session->displayMessage($message);

        yield from [];
    }

    public static function getInternalName() : string
    {
        return "tpaforce";
    }
}