<?php

namespace Startwind\Forrest\Repository;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\CommandFactory;

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
     */
    public function searchByFile(array $files): array
    {
        $response = $this->client->get($this->endpoint . 'search/file', ['verify' => false]);
        $plainCommands = json_decode($response->getBody(), true);

        $commandsArray = $plainCommands['commands'];

        $commands = [];

        foreach ($commandsArray as $commandsArrayElement) {
            $commands[$commandsArrayElement['name']] = CommandFactory::fromArray($commandsArrayElement);
        }

        return $commands;
    }

    /**
     * @inheritDoc
     */
    public function searchByPattern(array $patterns): array
    {
        $response = $this->client->get($this->endpoint . 'search/pattern', ['verify' => false]);
        $plainCommands = json_decode($response->getBody(), true);

        $commandsArray = $plainCommands['commands'];

        $commands = [];

        foreach ($commandsArray as $commandsArrayElement) {
            $commands[$commandsArrayElement['name']] = CommandFactory::fromArray($commandsArrayElement);
        }

        return $commands;
    }

    /**
     * @inheritDoc
     */
    public function searchByTools(array $tools): array
    {
        $response = $this->client->get($this->endpoint . 'search/tool', ['verify' => false]);
        $plainCommands = json_decode($response->getBody(), true);

        $commandsArray = $plainCommands['commands'];

        $commands = [];

        foreach ($commandsArray as $commandsArrayElement) {
            $commands[$commandsArrayElement['name']] = CommandFactory::fromArray($commandsArrayElement);
        }

        return $commands;
    }

    public function getCommand(string $identifier): Command
    {
        $response = $this->client->get($this->endpoint . 'command/' . urlencode($identifier), ['verify' => false]);
        $plainCommands = json_decode($response->getBody(), true);

        $command = CommandFactory::fromArray($plainCommands['command']);

        return $command;
    }

    public function assertHealth(): void
    {
        try {
            $this->client->get($this->endpoint . 'health', ['verify' => false]);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Unable to connect to Forrest API (' . $this->endpoint . ')');
        }
    }
}
