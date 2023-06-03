<?php

namespace Startwind\Forrest\Repository\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Startwind\Forrest\Command\Answer\Answer;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\CommandFactory;
use Startwind\Forrest\Command\Tool\Tool;
use Startwind\Forrest\Logger\ForrestLogger;
use Startwind\Forrest\Repository\QuestionAware;
use Startwind\Forrest\Repository\Repository;
use Startwind\Forrest\Repository\SearchAware;
use Startwind\Forrest\Repository\ToolAware;
use Startwind\Forrest\Util\OSHelper;

class ApiRepository implements Repository, SearchAware, ToolAware, QuestionAware
{
    public function __construct(
        protected readonly string $endpoint,
        private readonly string   $name,
        private readonly string   $description,
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
            ForrestLogger::warn('Unable to connect to Forrest API ("' . $this->endpoint . '"): ' . $exception->getMessage());
            throw new \RuntimeException('Unable to connect to Forrest API ("' . $this->endpoint . '")');
        }
    }

    /**
     * @inheritDoc
     */
    public function findToolInformation(string $tool): Tool|bool
    {
        try {
            $response = $this->client->get($this->endpoint . 'tool/' . urlencode($tool), ['verify' => false]);
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() == 404) {
                return false;
            }

            ForrestLogger::warn('Unable to get tool information. ' . $exception->getMessage());
            return false;
        }

        $information = json_decode((string)$response->getBody(), true);

        $toolInfo = new Tool($tool, $information['tool']['description']);

        if (array_key_exists('see', $information['tool'])) {
            $toolInfo->setSee($information['tool']['see']);
        }

        return $toolInfo;
    }

    public function ask(string $question): array
    {
        $payload = [
            'os' => OSHelper::getOS(),
            'question' => $question
        ];

        try {
            $response = $this->client->post($this->endpoint . 'ai/ask', [RequestOptions::JSON => $payload, 'verify' => false]);
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() == 404) {
                return [];
            }
            ForrestLogger::warn('Unable to ask:  ' . $exception->getMessage());
            return [];
        }

        $information = json_decode((string)$response->getBody(), true);
        $answer = $information['suggestion'];

        $answers[] = new Answer($answer['command'], $question, $answer['text']);

        return $answers;
    }

    public function explain(string $prompt): array
    {
        $payload = [
            'prompt' => $prompt
        ];

        try {
            $response = $this->client->post($this->endpoint . 'ai/explain', [RequestOptions::JSON => $payload, 'verify' => false]);
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() == 404) {
                return [];
            }
            ForrestLogger::warn('Unable to explain:  ' . $exception->getMessage());
            return [];
        }

        $result = json_decode((string)$response->getBody(), true);

        $answer = $result['suggestion'];

        $answers[] = new Answer($prompt, $prompt, $answer['text']);

        return $answers;
    }
}
