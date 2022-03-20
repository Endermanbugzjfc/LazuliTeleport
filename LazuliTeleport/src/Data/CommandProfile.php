<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class CommandProfile
{
    public string $name;

    public string $description;

    /** @var string[] */
    public array $aliases;

    /**
     * @param string[] $aliases
     */
    public static function create(
        string $name,
        string $description,
        array $aliases = []
    ) : self {
        $self = new self();
        $self->name = $name;
        $self->description = $description;
        $self->aliases = $aliases;

        return $self;
    }
}
