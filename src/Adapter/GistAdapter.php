<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
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

    public function __construct(string $username, string $prefix)
    {
        $this->username = $username;
        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function setClient(Client $client)
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
    private function getRawGists(string $username)
    {
        $response = $this->client->get(sprintf(self::GIST_URL, $username));
        return json_decode((string)$response->getBody(), true);
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
}
