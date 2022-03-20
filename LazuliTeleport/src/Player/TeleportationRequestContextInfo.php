<?php

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\Player\PlayerSessionInfo;
use SOFe\InfoAPI\Info;

final class TeleportationRequestContextInfo extends Info {

	public function __construct(
		protected PlayerSessionInfo $requestor,
		protected PlayerSessionInfo $requestee
	) {
	}
}