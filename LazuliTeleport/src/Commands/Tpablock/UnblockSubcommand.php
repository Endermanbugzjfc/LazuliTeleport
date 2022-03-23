<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands\Tpablock;

use Endermanbugzjfc\LazuliTeleport\Commands\BaseSubCommand;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerFinderActionInterface;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Generator;
use pocketmine\command\CommandSender;
use Ramsey\Uuid\UuidInterface;

class UnblockSubcommand extends BaseSubCommand implements PlayerFinderActionInterface
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
        return "tpablock.unblock";
    }

    public function getMaxTargetsLimit() : int
    {
        // TODO: Implement getMaxTargetsLimit() method.
    }

    public function runWithSelectedTargets(
        PlayerSession $session,
        UuidInterface ...$targets
    ) : void {
        // TODO: Implement runWithSelectedTargets() method.
    }

    public function getActionDisplayName(PlayerSession $session) : string
    {
        // TODO: Implement getActionDisplayName() method.
    }

    public function isActionAvailable(
        PlayerSession $session,
        UuidInterface ...$targets
    ) : Generator
    {
        // TODO: Implement isActionAvailable() method.
    }
}