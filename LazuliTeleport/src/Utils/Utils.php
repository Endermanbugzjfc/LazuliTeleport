<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Utils;

use ReflectionClass;
use ReflectionProperty;
use SOFe\AwaitGenerator\Channel;

final class Utils
{
    private function __construct()
    {
    }

    /**
     * @template T
     * @param Channel<T>|null $channel
     * @param T $value
     * @return Channel<T>|null
     */
    public static function closeChannel(
        ?Channel $channel,
        mixed $value
    ) : ?Channel {
        if ($channel !== null) {
            $queueSize = $channel->getReceiveQueueSize();
            for ($receiver = 0; $receiver < $queueSize; $receiver++) {
                $channel->sendWithoutWait($value);
            }
        }

        return $channel;
    }

    /**
     * @template T of object
     * @param T $toBeOverriden To be overriden.
     * @param T $valuesProvider
     */
    public static function override(
        object $toBeOverriden,
        object $valuesProvider
    ) : void {
        $reflection = new ReflectionClass(
            $valuesProvider
        );
        $properties = $reflection->getProperties(
            ReflectionProperty::IS_PUBLIC
        );
        foreach ($properties as $property) {
            if (
                !$property->isInitialized($valuesProvider)
                or
                ($value = $property->getValue($valuesProvider)) === null
            ) {
                continue;
            }

            $property->setValue($toBeOverriden, $value);
        }
    }
}