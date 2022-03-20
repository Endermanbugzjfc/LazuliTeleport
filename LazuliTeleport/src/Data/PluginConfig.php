<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

use Endermanbugzjfc\ConfigStruct\ListType;

class PluginConfig {

	/**
	 * @var CommandProfile[]
	 */
    #[ListType(CommandProfile::class)]
    public array $commands;

    public int $waitTimeAfterAcceptRequest = 60;

    public int $requestTimeout = 1200;
}