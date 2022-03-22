<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class Form
{
    public ?FormPage $fallback = null;
    public ?FormPage $findPlayer = null;
    public ?FormPage $listPlayer = null;
    public ?FormPage $pageViewTeleportationRequest = null;
}