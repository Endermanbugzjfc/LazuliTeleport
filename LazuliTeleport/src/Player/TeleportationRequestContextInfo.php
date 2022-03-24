<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use pocketmine\Server;
use RuntimeException;
use SOFe\InfoAPI\CommonInfo;
use SOFe\InfoAPI\DurationInfo;
use SOFe\InfoAPI\Info;
use SOFe\InfoAPI\InfoAPI;
use SOFe\InfoAPI\PositionInfo;
use SOFe\InfoAPI\StringInfo;
use SOFe\InfoAPI\TimeInfo;

final class TeleportationRequestContextInfo extends Info
{
    public function __construct(
        protected StringInfo $requestor,
        protected StringInfo $requestee,
        protected PositionInfo $requestorPosition,
        protected PositionInfo $requesteePosition,
        protected TimeInfo $requestSendTime,
        protected DurationInfo $teleportationWaitDuration,
        protected StringInfo $requestType,
    ) {
    }

    public function toString() : string
    {
        throw new RuntimeException(self::class . " must not be returned as a provided info");
    }

    public static function init() : void
    {
        $pluginName = LazuliTeleport::getInstance()->getName();
        InfoAPI::provideInfo(
            self::class,
            StringInfo::class,
            "$pluginName.TeleportationRequest.Requestor",
            fn(self $info) : StringInfo => $info->requestor
        );
        InfoAPI::provideInfo(
            self::class,
            StringInfo::class,
            "$pluginName.TeleportationRequest.Requestee",
            fn(self $info) : StringInfo => $info->requestor
        );
        InfoAPI::provideInfo(
            self::class,
            PositionInfo::class,
            "$pluginName.TeleportationRequest.RequesteePosition",
            fn(self $info) : PositionInfo => $info->requestorPosition
        );
        InfoAPI::provideInfo(
            self::class,
            PositionInfo::class,
            "$pluginName.TeleportationRequest.RequesteePosition",
            fn(self $info) : PositionInfo => $info->requestorPosition
        );
        InfoAPI::provideInfo(
            self::class,
            PositionInfo::class,
            "$pluginName.TeleportationRequest.RequesteePosition",
            fn(self $info) : PositionInfo => $info->requestorPosition
        );
        InfoAPI::provideInfo(
            self::class,
            TimeInfo::class,
            "$pluginName.TeleportationRequest.RequestSendTime",
            fn(self $info) : TimeInfo => $info->requestSendTime
        );
        InfoAPI::provideInfo(
            self::class,
            DurationInfo::class,
            "$pluginName.TeleportationRequest.TeleportationWaitDuration",
            fn(self $info) : DurationInfo => $info->teleportationWaitDuration
        );
        InfoAPI::provideInfo(
            self::class,
            StringInfo::class,
            "$pluginName.TeleportationRequest.RequestType",
            fn(self $info) : StringInfo => $info->requestType
        );
        InfoAPI::provideFallback(
            self::class,
            CommonInfo::class,
            fn(self $info) : CommonInfo => new CommonInfo(Server::getInstance())
        );
    }
}