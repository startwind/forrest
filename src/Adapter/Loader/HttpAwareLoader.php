<?php

namespace Startwind\Forrest\Adapter\Loader;

use GuzzleHttp\Client;

interface HttpAwareLoader
{
    /**
     * Inject an initiated client to the loader.
     */
    public function setClient(Client $client): void;
}
