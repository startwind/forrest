<?php

namespace Startwind\Forrest\Adapter;

class AdapterFactory
{
    static private array $adapters = [
        GistAdapter::TYPE => GistAdapter::class
    ];

    static public function getAdapter($adapterType, array $config): Adapter
    {
        if (!array_key_exists($adapterType, self::$adapters)) {
            throw new \RuntimeException("The adapter type $adapterType is not know. Allowed types are " . implode(', ', array_keys(self::$adapters) . '.'));
        }

        $adapterClass = self::$adapters[$adapterType];

        return call_user_func(array($adapterClass, 'fromConfigArray'), $config);
    }
}
