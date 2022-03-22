<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

use Endermanbugzjfc\ConfigStruct\KeyName;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

class Commands
{
    public CommandProfile $tpa;
    public CommandProfile $tpahere;
    public CommandProfile $tpaccept;
    public CommandProfile $tpareject;
    public CommandProfile $tpaforce;

    public CommandProfile $tpablock;
    #[KeyName("tpablock.list")]
    public CommandProfile $list;
    #[KeyName("tpablock.block")]
    public CommandProfile $block;
    #[KeyName("tpablock.unblock")]
    public CommandProfile $unblock;

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
                "tpaforce" => "(Admin command) Automatically accept teleportation request you send",

                "tpablock" => "Block another player from sending you teleportation request",
                "list" => "List players blocked by you",
                "block" => "Block one or more players",
                "unblock" => "Unblock one or more players",

                default => throw new RuntimeException("Unknown command \"$name\"")
            };
            $aliases = match ($name) {
                "tpahere" => [
                    "tphere"
                ],
                "tpareject" => [
                    "tpreject",
                    "tpadeny",
                    "tpdeny"
                ],
                default => []
            };
            $profile = CommandProfile::create($name, $description, $aliases);
            $property->setValue(
                $this,
                $profile
            );
        }
    }
}