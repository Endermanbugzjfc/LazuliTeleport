<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

use Endermanbugzjfc\ConfigStruct\ListType;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use function uasort;

class PluginConfig
{

    public int $requestTimeout = 60;

    public bool $repeatDurationMessage = false;

    public bool $canSendRequestBeforeResolve = true;

    public bool $autoHideCommands = true;

    /**
     * @var PermissionDependentOption[]
     * @phpstan-var array<int|string, PermissionDependentOption> This is supposed to have only string keys. However, it is user-defined, so the actual value might be int.
     */
    #[ListType(PermissionDependentOption::class)]
    public array $permissionDependentOptions = [];

    public int $waitDurationSliderMin = 0;
    public int $waitDurationSliderStep = 1;
    public int $waitDurationSliderTotalSteps = 0;

    public bool $pressEscapeToCancelRequest = false;

    public function __construct()
    {
        $pluginName = LazuliTeleport::getInstance()->getName();
        $this->permissionDependentOptions[""] =
        $this->permissionDependentOptions["$pluginName.group.name"] = PermissionDependentOption::getDefault();
    }

    /**
     * Ascending priority.
     * @return PermissionDependentOption[]
     * @phpstan-return array<int|string, PermissionDependentOption>
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