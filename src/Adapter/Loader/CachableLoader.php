<?php

namespace Startwind\Forrest\Adapter\Loader;

interface CachableLoader
{
    /**
     * Get a unique key for the caching layer.
     */
    public function getCacheKey(): string;
}
