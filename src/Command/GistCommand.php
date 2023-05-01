<?php

namespace Startwind\Forrest\Command;

use GuzzleHttp\Client;

class GistCommand extends Command
{
    private string $rawUrl;

    private Client $client;

    public function __construct(string $name, string $description, string $rawUrl, Client $client)
    {
        $this->rawUrl = $rawUrl;
        $this->client = $client;

        parent::__construct($name, $description, '');
    }

    public function getPrompt(): string
    {
        $response = $this->client->get($this->rawUrl);
        return (string)$response->getBody();
    }
}
