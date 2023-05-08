<?php

namespace Startwind\Forrest\Command;

use GuzzleHttp\Client;

class GistCommand extends Command
{
    public function __construct(
        string $name,
        string $description,
        private readonly string $rawUrl,
        private readonly Client $client
    ) {
        parent::__construct($name, $description, new Prompt(''));
    }

    /**
     * @inheritDoc
     */
    public function getPrompt(array $values = []): Prompt
    {
        $response = $this->client->get($this->rawUrl);
        return new Prompt((string)$response->getBody());
    }
}
