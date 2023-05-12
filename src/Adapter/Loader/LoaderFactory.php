<?php

namespace Startwind\Forrest\Adapter\Loader;

class LoaderFactory
{
    private static array $loaders = [
        'github' => PrivateGitHubLoader::class
    ];

    /**
     * Create a loader depending on a given configuration array.
     */
    public static function create($config): Loader
    {
        if (!array_key_exists($config['type'], self::$loaders)) {
            throw new \RuntimeException('No YAML loader found with the identifier "' . $config['loader']['type'] . '". Known types are ' . implode(', ', array_keys(self::$loaders)) . '.');
        }

        $loaderClass = self::$loaders[$config['type']];

        /** @var Loader $loader */
        $loader = call_user_func([$loaderClass, 'fromConfigArray'], $config['config']);

        return $loader;
    }
}
