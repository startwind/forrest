<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;

interface Adapter
{
    /**
     * Return the type of the adapter
     */
    public function getType(): string;

    public function assertHealth(): void;

    public function getCommand(string $identifier): Command;

    /**
     * Return an initialized adapter via config array.
     */
    public static function fromConfigArray(array $config, Client $client): Adapter;
}
