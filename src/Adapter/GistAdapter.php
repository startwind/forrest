<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Adapter\Exception\RateLimitExceededExceededException;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\GistCommand;

class GistAdapter implements Adapter, ClientAwareAdapter
{
    const TYPE = 'gist';

    const GIST_URL = 'https://api.github.com/users/%s/gists';

    const GIST_FIELD_RAW_URL = 'raw_url';

    private Client $client;

    private string $username;
    private string $prefix;

    private string $rawGist = "";

    public function __construct(string $username, string $prefix)
    {
        $this->username = $username;
        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * Return the raw gists from
     */
    private function getRawGists(string $username): array
    {
        if (!$this->rawGist) {
            try {
                $response = $this->client->get(sprintf(self::GIST_URL, $username));
            } catch (\Exception $exception) {
                if (str_contains($exception->getMessage(), 'rate limit exceeded')) {
                    throw new RateLimitExceededExceededException('Gist API rate limit exceeded.');
                } else {
                    throw $exception;
                }
            }
            $this->rawGist = json_decode((string)$response->getBody(), true);
        }

        return $this->rawGist;
    }

    /**
     * @inheritDoc
     */
    public function getCommands(): array
    {
        $gists = $this->getRawGists($this->username);

        $commands = [];

        foreach ($gists as $gist) {
            if (str_starts_with($gist['description'], $this->prefix)) {
                foreach ($gist['files'] as $file) {
                    $name = $file['filename'];
                    $description = str_replace($this->prefix, '', $gist['description']);
                    $rawUrl = $file[self::GIST_FIELD_RAW_URL];
                    $commands[] = new GistCommand($name, $description, $rawUrl, $this->client);
                }
            }
        }

        return $commands;
    }

    /**
     * @inheritDoc
     */
    static public function fromConfigArray(array $config): GistAdapter
    {
        return new self($config['username'], $config['prefix']);
    }

    /**
     * @inheritDoc
     */
    public function isEditable(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function addCommand(Command $command): void
    {
        throw new \RuntimeException('Unable to add a command to a GIST repository.');
    }

    /**
     * @inheritDoc
     */
    public function removeCommand(string $commandName): void
    {
        throw new \RuntimeException('Unable to remove a command to a GIST repository.');
    }
}
