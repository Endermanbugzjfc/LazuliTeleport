<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

class Commands
{
    public CommandProfile $tpa;
    public CommandProfile $tpahere;
    public CommandProfile $tpaccept;
    public CommandProfile $tpareject;
    public CommandProfile $tpablock;
    public CommandProfile $tpaforce;

    public function __construct()
    {
        $reflection = new ReflectionClass(self::class);
        $filter = ReflectionProperty::IS_PUBLIC;
        $properties = $reflection->getProperties($filter);
        foreach ($properties as $property) {
            $name = $property->getName();
            $description = match ($name) {
                "tpa" => "Request teleportation to another player",
                "tpahere" => "Request teleporting another player to you",
                "tpaccept" => "Accept a teleportation request",
                "tpareject" => "Reject a teleportation request",
                "tpablock" => "Block another player from sending you teleportation request",
                "tpaforce" => "(Admin command) Automatically accept teleportation request you send",
                default => throw new RuntimeException("Unknown command \"$name\"")
            };
            $profile = CommandProfile::create($name, $description);
            $property->setValue(
                $this,
                $profile
            );
        }
    }
}