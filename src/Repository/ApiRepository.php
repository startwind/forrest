<?php

namespace Startwind\Forrest\Repository;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Parameters\FileParameter;
use Startwind\Forrest\Runner\CommandRunner;

class ApiRepository implements Repository, SearchAware
{
    public function __construct(
        private readonly string $endpoint,
        private readonly string $name,
        private readonly string $description,
        private readonly Client $client,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function isSpecial(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function searchByFile(array $files): array
    {
        $response = $this->client->get($this->endpoint . 'search/file', ['verify' => false]);
        $plainCommands = json_decode($response->getBody(), true);



        var_dump($plainCommands);
        die;
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function searchByPattern(array $patterns): array
    {

    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function searchByTools(array $tools): array
    {

    }
}
