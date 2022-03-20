<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class PermissionDependentOption
{
    public ?int $waitDurationAfterAcceptRequest = null;
    public ?int $tpahereRequesteeLimit = null;

    public static function getDefault() : self
    {
        $self = new self();
        $self->waitDurationAfterAcceptRequest = 60;
        $self->tpahereRequesteeLimit = 1200;

        return $self;
    }
}