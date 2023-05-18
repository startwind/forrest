<?php

namespace Startwind\Forrest\Adapter\Loader\Decorator;

use Psr\Cache\CacheItemPoolInterface;
use Startwind\Forrest\Adapter\Loader\CachableLoader;
use Startwind\Forrest\Adapter\Loader\Loader;

class CacheDecorator implements Loader
{
    private const TIME_TO_LIVE = 60 * 60;

    protected Loader $loader;

    protected CacheItemPoolInterface $cacheItemPool;

    public function __construct(Loader $loader, CacheItemPoolInterface $cacheItemPool)
    {
        $this->loader = $loader;
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function load(): string
    {
        if ($this->loader instanceof CachableLoader) {
            $key = $this->loader->getCacheKey();
            $item = $this->cacheItemPool->getItem($key);
            if ($item->isHit()) {
                var_dump('HIT');
                return $item->get();
            } else {
                var_dump('NO HIT');
                $content = $this->loader->load();
                $item->set($content);
                $item->expiresAfter(self::TIME_TO_LIVE);
                $this->cacheItemPool->save($item);
                return $content;
            }
        } else {
            return $this->loader->load();
        }
    }

    public static function fromConfigArray(array $config): Loader
    {
        throw new \RuntimeException('This is just a decorator and works only with an already initiated Loader.');
    }
}
