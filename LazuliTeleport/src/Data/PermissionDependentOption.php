<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

use Endermanbugzjfc\LazuliTeleport\Utils\NoOverride;

class PermissionDependentOption
{
    /**
     * @var int Bigger number = higher priorty. Overrides the option value with a lower priority.
     */
    #[NoOverride]
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

    #[NoOverride]
    public ?string $formFile = null;
    #[NoOverride]
    public ?string $messagesFile = null;

    public ?bool $enablePlayerFinder = null;

    public ?int $waitDurationFormSliderMin = null;
    public int $waitDurationFormSliderStep = null;
    public int $waitDurationFormSliderTotalSteps = null;

    public static function getDefault() : self
    {
        $self = new self();
        $self->waitDurationAfterAcceptRequest = 3;
        $self->tpahereRequesteeLimit = 0;
        $self->tpaCoolDown = $self->tpahereCoolDown = 10;
        $self->enablePlayerFinder = true;

        $self->waitDurationFormSliderMin = 0;
        $self->waitDurationFormSliderStep = 1;
        $self->waitDurationFormSliderTotalSteps = 20;

        return $self;
    }
}