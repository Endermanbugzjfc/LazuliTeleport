<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

class FormSession
{
    public function __construct(
        protected PlayerSession $playerSession
    ) {
    }

    public function getPlayerSession() : PlayerSession
    {
        return $this->playerSession;
    }
}