<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use RuntimeException;
use SOFe\InfoAPI\CommonInfo;
use SOFe\InfoAPI\DurationInfo;
use SOFe\InfoAPI\Info;
use SOFe\InfoAPI\InfoAPI;
use pocketmine\Server;

final class TeleportationRequestContextInfo extends Info
{
    public function __construct(
        protected PlayerSessionInfo $requestor,
        protected PlayerSessionInfo $requestee,
        protected DurationInfo $teleportationWaitDuration,

        protected bool $blocked
    ) {
    }

    public function getBlocked() : bool {
    	return $this->blocked;
    }

    public function toString() : string
    {
        throw new RuntimeException(self::class . " must not be returned as a provided info");
    }

    public static function init() : void {
    	$pluginName = LazuliTeleport::getInstance()->getName();
    	InfoAPI::provideInfo(
    		self::class,
    		PlayerSessionInfo::class,
    		"$pluginName.TeleportationRequest.Requestor",
    		fn(self $info) : PlayerSessionInfo => $info->requestor
    	);
    	InfoAPI::provideInfo(
    		self::class,
    		PlayerSessionInfo::class,
    		"$pluginName.TeleportationRequest.Requestee",
    		fn(self $info) : PlayerSessionInfo => $info->requestor
    	);
    	InfoAPI::provideInfo(
    		self::class,
    		DurationInfo::class,
    		"$pluginName.TeleportationRequest.TeleportationWaitDuration",
    		fn(self $info) : DurationInfo => $info->teleportationWaitDuration
    	);
    	InfoAPI::provideFallback(
    		self::class,
    		CommonInfo::class,
    		fn(self $info) : CommonInfo => new CommonInfo(Server::getInstance())
    	);
    }
}