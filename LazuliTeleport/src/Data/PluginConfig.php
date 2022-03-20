<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

use Endermanbugzjfc\ConfigStruct\ListType;

class PluginConfig
{

    /**
     * @var CommandProfile[]
     */
    #[ListType(CommandProfile::class)]
    public array $commands;

    /**
     * @var int[]
     * @phpstan-var array<string, int>
     */
    public array $waitTimeAfterAcceptRequest = [
        "" => 60
    ];

    public int $requestTimeout = 1200;
}