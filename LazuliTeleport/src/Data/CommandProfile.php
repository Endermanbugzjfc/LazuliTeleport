<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class CommandProfile
{
    public string $name;

    public string $description;

    /** @var string[] */
    public array $aliases;
}
