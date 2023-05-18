<?php

namespace Startwind\Forrest\Adapter\Loader\Decorator;

use Psr\Cache\CacheItemPoolInterface;
use Startwind\Forrest\Adapter\Loader\Loader;
use Startwind\Forrest\Adapter\Loader\WritableLoader;

class WritableCacheDecorator extends CacheDecorator implements WritableLoader
{
    protected Loader $loader;

    public function __construct(Loader $loader, CacheItemPoolInterface $cacheItemPool)
    {
        if (!$loader instanceof WritableLoader) {
            throw new \RuntimeException('The given loader does not implement the WritableLoader interface. This is required.');
        }

        parent::__construct($loader, $cacheItemPool);
    }


    public function write(string $content)
    {
        $this->loader->write($content);
    }
}
