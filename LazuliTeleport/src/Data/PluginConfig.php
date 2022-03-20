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
    public array $waitDurationAfterAcceptRequest = [
        "" => 60
    ];

    /**
     * @var int[]
     * @phpstan-var array<string, int>
     */
    public array $tpahereRequesteeLimit = [
        "" => 0
    ];

    public int $requestTimeout = 1200;

    public bool $repeatDurationMessage = false;
}