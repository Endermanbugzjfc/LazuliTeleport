<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Utils;

use SOFe\AwaitGenerator\Channel;

final class Utils
{
    private function __construct()
    {
    }

    /**
     * @param Channel<null>|null $channel
     * @return Channel<null>|null
     */
    public static function closeChannel(
    	?Channel $channel
    ) : ?Channel {
    	if ($channel !== null) {
	    	$queueSize = $channel->getReceiveQueueSize();
	    	for ($receiver = 0; $receiver < $queueSize; $receiver++) {
	    		$channel->sendWithoutWait(null);
	    	}
    	}

    	return $channel;
    }
}