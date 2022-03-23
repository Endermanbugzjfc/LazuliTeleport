<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Generator;
use Ramsey\Uuid\UuidInterface;

interface PlayerFinderActionInterface
{
    /**
     * @param string ...$targets Player names. Case might not be accurate. All offline player's names are in lowercase.
     */
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

    /**
     * @return Generator<mixed, mixed, mixed, static>
     */
    public static function getInstance() : Generator;
}