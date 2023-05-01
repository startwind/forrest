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

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    private function getRawGists(string $username)
    {
        $response = $this->client->get(sprintf(self::GIST_URL, $username));
        return json_decode((string)$response->getBody(), true);
    }

    public function getCommands($withActualCommand = true): array
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
     * Return the raw content of the given gist.
     */
    private function getRawContent(string $rawUrl): string
    {
        $client = new Client();
        $response = $client->get($rawUrl);
        return (string)$response->getBody();
    }

    static public function fromConfigArray(array $config): GistAdapter
    {
        return new self($config['username'], $config['prefix']);
    }
}
