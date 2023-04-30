<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;

interface ClientAwareAdapter
{
    public function setClient(Client $client);
}
