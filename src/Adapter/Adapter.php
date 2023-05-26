<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;

interface Adapter
{
    /**
     * Return the type of the adapter
     */
    public function getType(): string;

    /**
     * Return an initialized adapter via config array.
     */
    public static function fromConfigArray(array $config, Client $client): Adapter;
}
