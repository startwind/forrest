<?php

namespace Startwind\Forrest\Adapter\Loader;

interface Loader
{
    /**
     * Load the configuration and return it as a string.
     */
    public function load(): string;

    public function assertHealth(): void;

    /**
     * Create a loader via a config array.
     */
    public static function fromConfigArray(array $config): Loader;
}
