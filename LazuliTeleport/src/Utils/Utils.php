<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Utils;

use pocketmine\Server;
use ReflectionClass;
use ReflectionProperty;
use SOFe\AwaitGenerator\Channel;
use function count;
use function stripos;
use function strlen;
use function strtolower;
use const PHP_INT_MAX;

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

    /**
     * Return the index that is closer to left if the elements count is even.
     * @param mixed[] $array
     */
    public static function getArrayMiddleIndex(
        array $array
    ) : int {
        // Credit: https://thisinterestsme.com/middle-index-php-array/
        $lastIndex = count($array) - 1;
        $divided = $lastIndex / 2;
        return (int)$divided;
    }

    /**
     * A modified version of {@link Server::getPlayerByPrefix()}.
     * @template T
     * @param string[] $candidates
     * @phpstan-param array<T, string> $candidates Case-insensitive.
     * @return T|null Index of $candidates.
     */
    public function getStringByPrefix(
        array $candidates,
        string $find
    ) : mixed {
        $found = null;
        $find = strtolower($find);
        $delta = PHP_INT_MAX;
        foreach ($candidates as $index => $candidate) {
            if (stripos($candidate, $find) === 0) {
                $curDelta = strlen($candidate) - strlen($find);
                if ($curDelta < $delta) {
                    $found = $index;
                    $delta = $curDelta;
                }
                if ($curDelta === 0) {
                    break;
                }
            }
        }

        return $found;
    }
}