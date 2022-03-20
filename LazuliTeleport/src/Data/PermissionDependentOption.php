<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class PermissionDependentOption
{
    /**
     * @var int Bigger number = higher priorty. Overrides the option value with a lower priority.
     */
    public int $priority = 0;

    /*
     * To anyone who is using ConfigStruct:
     * Default value of a nullable property should always be null.
     * Otherwise if the user does not set a value, the default value will be used.
     */
    public ?int $waitDurationAfterAcceptRequest = null;

    public ?int $tpaCoolDown = null;

    public ?int $tpahereRequesteeLimit = null;
    public ?int $tpahereCoolDown = null;

    public static function getDefault() : self
    {
        $self = new self();
        $self->waitDurationAfterAcceptRequest = 3;
        $self->tpahereRequesteeLimit = 0;
        $self->tpaCoolDown = $self->tpahereCoolDown = 10;

        return $self;
    }
}