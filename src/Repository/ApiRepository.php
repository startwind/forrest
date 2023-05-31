<?php

namespace Startwind\Forrest\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\CommandFactory;

class ApiRepository implements Repository, SearchAware
{
    public function __construct(
        protected readonly string $endpoint,
        private readonly string $name,
        private readonly string $description,
        protected readonly Client $client,
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
     * @todo merge the three search functions and remove duplicate code
     *
     * @inheritDoc
     */
    public function searchByFile(array $files): array
    {
        $payload = [
            'types' => $files
        ];

        $response = $this->client->post(
            $this->endpoint . 'search/file',
            [
                RequestOptions::JSON => $payload,
                'verify' => false
            ]
        );

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
        $payload = [
            'patterns' => $patterns
        ];

        $response = $this->client->post(
            $this->endpoint . 'search/pattern',
            [
                RequestOptions::JSON => $payload,
                'verify' => false
            ]
        );

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
        $payload = [
            'tool' => $tools[0]
        ];

        $response = $this->client->post(
            $this->endpoint . 'search/tool',
            [
                RequestOptions::JSON => $payload,
                'verify' => false
            ]
        );

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
            throw new \RuntimeException('Unable to connect to Forrest API ("' . $this->endpoint . '")');
        }
    }
}
