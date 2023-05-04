<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;

class AdapterFactory
{
    private static array $adapters = [
        GistAdapter::TYPE => GistAdapter::class,
        YamlAdapter::TYPE => YamlAdapter::class,
    ];

    public static function getAdapter(string $adapterType, array $config, Client $client): Adapter
    {
        if (!array_key_exists($adapterType, self::$adapters)) {
            throw new \RuntimeException("The adapter type $adapterType is not know. Allowed types are " . implode(', ', array_keys(self::$adapters)) . '.');
        }

        $adapterClass = self::$adapters[$adapterType];

        /** @var Adapter $adapter */
        $adapter = call_user_func([$adapterClass, 'fromConfigArray'], $config);

        if ($adapter instanceof ClientAwareAdapter) {
            $adapter->setClient($client);
        }

        return $adapter;
    }
}
