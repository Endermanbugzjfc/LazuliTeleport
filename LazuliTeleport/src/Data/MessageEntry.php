<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class MessageEntry
{
    public string $chat = "";
    public string $title = "";
    public string $subtitle = "";
    public string $actionbar = "";
    public ?int $fadeIn = null;
    public ?int $stay = null;
    public ?int $fadeOut = null;
}