<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands\Tpablock;

use CortexPE\Commando\args\TextArgument;
use Generator;
use pocketmine\command\CommandSender;

class TpablockCommand extends BaseCommand
{
    public const PLAYERS = "Players (separate with comma)";

    protected function prepare() : void {
        $this->registerArgument(0, new TextArgument);
    }

    /**
     * @param array|array<string,mixed|array<mixed>> $args
     * @phpstan-param array<string,mixed|array<mixed>> $args
     */
    protected function asyncRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : Generator {
        $session = $this->playerSession($sender);
    }

    public static function runWithSelectedTargets(
        PlayerSession $session,
        UuidInterface ...$targets
    ) : void {

    }

    public static function getInternalName() : string
    {
        return "tpablock";
    }
}