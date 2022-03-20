<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use SOFe\InfoAPI\DurationInfo;
use SOFe\InfoAPI\Info;
use SOFe\InfoAPI\InfoAPI;
use SOFe\InfoAPI\NumberInfo;
use SOFe\InfoAPI\PlayerInfo;

final class PlayerSessionInfo extends Info
{
    public function __construct(
        protected NumberInfo $tpaRequesteeLimit,
        protected DurationInfo $tpaCoolDown,

        protected NumberInfo $tpahereRequesteeLimit,
        protected DurationInfo $tpahereCoolDown,

        protected DurationInfo $teleportationWaitDuration,
        protected DurationInfo $forcedTeleportationWaitDuration,

        protected PlayerInfo $playerInfo
    ) {
    }

    public function toString() : string
    {
        return $this->playerInfo->toString();
    }

    public static function init() : void
    {
        $pluginName = LazuliTeleport::getInstance()->getName();
        InfoAPI::provideInfo(
            self::class,
            NumberInfo::class,
            "$pluginName.Player.TpaRequesteeLimit",
            fn(self $info) : NumberInfo => $info->tpaRequesteeLimit
        );
        InfoAPI::provideInfo(
            self::class,
            DurationInfo::class,
            "$pluginName.Player.TpaCoolDown",
            fn(self $info) : DurationInfo => $info->tpaCoolDown
        );
        InfoAPI::provideInfo(
            self::class,
            NumberInfo::class,
            "$pluginName.Player.TpahereRequesteeLimit",
            fn(self $info) : NumberInfo => $info->tpahereRequesteeLimit
        );
        InfoAPI::provideInfo(
            self::class,
            DurationInfo::class,
            "$pluginName.Player.TeleportationWaitDuration",
            fn(self $info) : DurationInfo => $info->teleportationWaitDuration
        );
        InfoAPI::provideInfo(
            self::class,
            DurationInfo::class,
            "$pluginName.Player.ForcedTeleportationWaitDuration",
            fn(self $info) : DurationInfo => $info->forcedTeleportationWaitDuration
        );
    }
}