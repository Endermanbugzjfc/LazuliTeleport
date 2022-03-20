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

    public static function createChat(
        string $content
    ) : self {
        $self = new self();
        $self->chat = $content;
        return $self;
    }

    public static function createTitle(
        string $content,
        string $subtitle = ""
    ) : self {
        $self = new self();
        $self->title = $content;
        $self->subtitle = $content;
        return $self;
    }

    public static function createActionbar(
        string $content
    ) : self {
        $self = new self();
        $self->actionbar = $content;
        return $self;
    }
}