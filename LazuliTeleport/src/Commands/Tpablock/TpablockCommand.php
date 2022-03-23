<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands\Tpablock;

use Endermanbugzjfc\LazuliTeleport\Commands\BaseCommand;
use Generator;
use pocketmine\command\CommandSender;

class TpablockCommand extends BaseCommand
{
    public const PLAYERS = "Players (separate with comma)";

    protected function asyncPrepare() : Generator
    {
        foreach ([
            ListSubcommand::getInstance(),
            BlockSubcommand::getInstance(),
            UnblockSubcommand::getInstance()
        ] as $command) {
            $this->registerSubCommand(yield from $command);
        }
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

    public static function getInternalName() : string
    {
        return "tpablock";
    }
}