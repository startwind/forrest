<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Adapter\Exception\RateLimitExceededException;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\GistCommand;

class GistAdapter implements Adapter, ClientAwareAdapter
{
    public const TYPE = 'gist';

    public const GIST_URL = 'https://api.github.com/users/%s/gists';

    public const GIST_FIELD_RAW_URL = 'raw_url';

    private Client $client;

    private array $rawGist = [];

    public function __construct(
        private readonly string $username,
        private readonly string $prefix
    ) {
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
        if (empty($this->rawGist)) {
            try {
                $response = $this->client->get(sprintf(self::GIST_URL, $username));
            } catch (\Exception $exception) {
                if (str_contains($exception->getMessage(), 'rate limit exceeded')) {
                    throw new RateLimitExceededException('Gist API rate limit exceeded.');
                } else {
                    throw $exception;
                }
            }
            // @todo: validate json before decode and serialize to an object
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
    public static function fromConfigArray(array $config): GistAdapter
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
