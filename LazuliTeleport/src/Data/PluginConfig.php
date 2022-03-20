<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

use Endermanbugzjfc\ConfigStruct\ListType;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use function uasort;

class PluginConfig
{

    /**
     * @var CommandProfile[]
     */
    #[ListType(CommandProfile::class)]
    public array $commands;

    public int $requestTimeout = 60;

    public bool $repeatDurationMessage = false;

    /**
     * @var PermissionDependentOption[]
     * @phpstan-var array<int|string, PermissionDependentOption> This is supposed to have only string keys. However, it is user-defined, so the actual value might be int.
     */
    #[ListType(PermissionDependentOption::class)]
    public array $permissionDependentOptions = [];

    public function __construct()
    {
        $pluginName = LazuliTeleport::getInstance()->getName();
        $this->commands = self::getDefaultCommandProfiles();
        $this->permissionDependentOptions[""] =
        $this->permissionDependentOptions["$pluginName.group.name"] = PermissionDependentOption::getDefault();
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
            ),
            CommandProfile::create(
                "tpablock",
                "Block another player from sending you teleportation request",
                [
                    "tpblock"
                ]
            )
        ] as $profile) {
            $name = $profile->name;
            $return[$name] = $profile;
        }

        return $return;
    }

    /**
     * Ascending priority.
     * @var PermissionDependentOption[]
     * @phpstan-var array<int|string, PermissionDependentOption>
     */
    public function getOrderedPermissionDependentOptions() : array
    {
        $clone = $this->permissionDependentOptions;
        uasort(
            $clone,
            fn(PermissionDependentOption $a, PermissionDependentOption $b) : int => $a->priority <=> $b->priority
         );
        return $clone;
    }
}