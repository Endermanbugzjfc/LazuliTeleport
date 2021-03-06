<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use Endermanbugzjfc\LazuliTeleport\Player\PlayerFinderActionInterface;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use Generator;
use pocketmine\command\CommandSender;
use Ramsey\Uuid\UuidInterface;

class TpahereCommand extends BaseCommand implements PlayerFinderActionInterface
{
    /**
     * @param array|array<string,mixed|array<mixed>> $args
     * @phpstan-param array<string,mixed|array<mixed>> $args
     */
    protected function asyncRun(
        CommandSender $sender,
        string $aliasUsed,
        array $args
    ) : Generator {
        // TODO: Implement onRun() method.
    }

    public static function getInternalName() : string
    {
        return "tpahere";
    }

    public function getMaxTargetsLimit() : int
    {
        // TODO: Implement getMaxTargetsLimit() method.
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