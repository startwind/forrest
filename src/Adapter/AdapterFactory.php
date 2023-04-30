<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;

class AdapterFactory
{
    static private array $adapters = [
        GistAdapter::TYPE => GistAdapter::class
    ];

    static public function getAdapter($adapterType, array $config, Client $client): Adapter
    {
        if (!array_key_exists($adapterType, self::$adapters)) {
            throw new \RuntimeException("The adapter type $adapterType is not know. Allowed types are " . implode(', ', array_keys(self::$adapters) . '.'));
        }

        $adapterClass = self::$adapters[$adapterType];

        /** @var \Startwind\Forrest\Adapter\Adapter $adapter */
        $adapter = call_user_func(array($adapterClass, 'fromConfigArray'), $config);

        if ($adapter instanceof ClientAwareAdapter) {
            $adapter->setClient($client);
        }

        return $adapter;
    }
}
