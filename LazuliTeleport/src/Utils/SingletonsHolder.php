<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Utils;

use Generator;
use SOFe\AwaitGenerator\Channel;

class SingletonsHolder
{
    /**
     * @var array<class-string, object>
     */
    protected array $singletons = [];

    public function register(
        object $singleton
    ) : void {
        $class = $singleton::class;
        $this->singletons[$class] = $singleton;
        $waitFor = $this->queue[$class] ?? null;
        Utils::closeChannel($waitFor, $singleton);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function get(
        string $class
    ) : object {
        /**
         * @var T
         */
        $object = $this->singletons[$class];
        return $object;
    }

    /**
     * @var Channel<object>[]
     * @phpstan-var array<class-string, Channel<object>>
     */
    protected array $queue;

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return Generator<mixed, mixed, mixed, T>
     */
    public function waitFor(
        string $class
    ) : Generator {
        $this->queue[$class] = $channel = new Channel();
        return yield from $channel->receive();
    }
}
