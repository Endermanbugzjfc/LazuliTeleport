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

    public function __construct()
    {
        $this->commands = self::getDefaultCommandProfiles();
    }

    /**
     * @return CommandProfile[]
     */
    public static function getDefaultCommandProfiles() : array
    {
        foreach ([
            CommandProfile::create(
                "tpa",
                "Request teleportation to another player"
            ),
            CommandProfile::create(
                "tpahere",
                "Request teleporting another player to you",
                [
                    "tphere"
                ]
            ),
            CommandProfile::create(
                "tpaccept",
                "Accept a teleportation request"
            ),
            CommandProfile::create(
                "tpareject",
                "Reject a teleportation request",
                [
                    "tpreject",
                    "tpadeny",
                    "tpdeny"
                ]
            )
        ] as $profile) {
            $name = $profile->name;
            $return[$name] = $profile;
        }

        return $return;
    }
}