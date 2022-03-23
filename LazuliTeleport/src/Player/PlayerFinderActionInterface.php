<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Generator;
use Ramsey\Uuid\UuidInterface;

interface PlayerFinderActionInterface
{
    public function runWithSelectedTargets(
        PlayerSession $session,
        string ...$targets
    ) : void;

    public function getActionDisplayName(
        PlayerSession $session
    ) : string;

    /**
     * @return Generator<mixed, mixed, mixed, bool>
     */
    public function isActionAvailable(
        PlayerSession $session,
        UuidInterface ...$targets
    ) : Generator;
}