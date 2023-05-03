<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;

interface ClientAwareAdapter
{
    /**
     * Inject an initialized Guzzle client
     */
    public function setClient(Client $client): void;
}
