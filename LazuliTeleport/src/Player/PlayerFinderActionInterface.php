<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Ramsey\Uuid\UuidInterface;

interface PlayerFinderActionInterface
{
    public function getMaxTargetsLimit() : int;

    public function runWithSelectedTargets(
        PlayerSession $session,
        UuidInterface ...$targets
    ) : void;
}