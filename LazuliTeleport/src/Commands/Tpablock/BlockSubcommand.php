<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands\Tpablock;

use Endermanbugzjfc\LazuliTeleport\Commands\BaseSubCommand;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerFinderActionInterface;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Generator;
use pocketmine\command\CommandSender;
use Ramsey\Uuid\UuidInterface;

class BlockSubcommand extends BaseSubCommand implements PlayerFinderActionInterface
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
        return "tpablock.block";
    }

    public function runWithSelectedTargets(
        PlayerSession $session,
        array $targets
    ) : void {
        // TODO: Implement runWithSelectedTargets() method.
    }

    public function getActionDisplayName(PlayerSession $session) : string
    {
        // TODO: Implement getActionDisplayName() method.
    }

    public function isActionAvailable(
        PlayerSession $session,
        array $targets
    ) : Generator
    {
        // TODO: Implement isActionAvailable() method.
    }
}