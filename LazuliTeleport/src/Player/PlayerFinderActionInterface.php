<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Generator;

interface PlayerFinderActionInterface
{
    /**
     * @param string[] $targets Player names. Case might not be accurate. All offline player's names are in lowercase.
     */
    public function runWithSelectedTargets(
        PlayerSession $session,
        array $targets
    ) : void;

    public function getActionDisplayName(
        PlayerSession $session
    ) : string;

    /**
     * @return Generator<mixed, mixed, mixed, bool>
     * @param string[] See {@link PlayerFinderActionInterface::runWithSelectedTargets()}.
     */
    public function isActionAvailable(
        PlayerSession $session,
        array $targets
    ) : Generator;
}