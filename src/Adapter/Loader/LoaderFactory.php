<?php

namespace Startwind\Forrest\Adapter\Loader;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Startwind\Forrest\Adapter\Loader\Decorator\CacheDecorator;
use Startwind\Forrest\Adapter\Loader\Decorator\WritableCacheDecorator;

class LoaderFactory
{
    private const CACHE_DIR = '/tmp/forrest-cache';

    private static array $loaders = [
        'github' => PrivateGitHubLoader::class,
        'localFile' => LocalFileLoader::class,
        'httpFile' => HttpFileLoader::class
    ];

    /**
     * Create a loader depending on a given configuration array.
     */
    public static function create($config, Client $client): Loader
    {
        if (!array_key_exists($config['type'], self::$loaders)) {
            throw new \RuntimeException('No YAML loader found with the identifier "' . $config['loader']['type'] . '". Known types are ' . implode(', ', array_keys(self::$loaders)) . '.');
        }

        $loaderClass = self::$loaders[$config['type']];

        /** @var Loader $loader */
        $loader = call_user_func([$loaderClass, 'fromConfigArray'], $config['config']);

        if ($loader instanceof HttpAwareLoader) {
            $loader->setClient($client);
        }

        return self::decorateWithCache($loader);
    }

    private static function decorateWithCache(Loader $loader): CacheDecorator
    {
        $filesystemAdapter = new Local(self::CACHE_DIR);
        $filesystem = new Filesystem($filesystemAdapter);
        $pool = new FilesystemCachePool($filesystem);

        if ($loader instanceof WritableLoader) {
            $loader = new WritableCacheDecorator($loader, $pool);
        } else {
            $loader = new CacheDecorator($loader, $pool);
        }

        return $loader;
    }
}
