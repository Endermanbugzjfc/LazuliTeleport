<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use SOFe\InfoAPI\Info;

final class PlayerSessionInfo extends Info
{
    public int $tpaRequesteeLimit;
    public int $tpaCoolDown;

    public int $tpahereRequesteeLimit;
    public int $tpahereCoolDown;

    public int $waitDuration;
    public int $forceWaitDuration;
}