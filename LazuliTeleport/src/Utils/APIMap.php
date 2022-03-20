<?php

namespace Endermanbugzjfc\LazuliTeleport\Utils;

class APIMap {

	/**
	 * @var array<class-string, object>
	 */
	protected array $singletons = [];

	public function register(
		object $singleton
	) : void {
		$class = $singleton::class;
		$this->singletons[$class] = $singleton;
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
}