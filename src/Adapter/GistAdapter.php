<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;

class GistAdapter implements Adapter
{
    const TYPE = 'gist';

    const GIST_URL = 'https://api.github.com/users/%s/gists';

    private string $username;
    private string $prefix;

    public function __construct(string $username, string $prefix)
    {
        $this->username = $username;
        $this->prefix = $prefix;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    private function getRawGists(string $username)
    {
        $client = new Client();
        $response = $client->get(sprintf(self::GIST_URL, $username));

        return json_decode((string)$response->getBody(), true);
    }

    public function getCommands(): array
    {
        $gists = $this->getRawGists($this->username);

        $commands = [];

        foreach ($gists as $gist) {
            if (str_starts_with($gist['description'], $this->prefix)) {
                foreach ($gist['files'] as $file) {
                    $name = $file['filename'];
                    $description = str_replace($this->prefix, '', $gist['description']);

                    $commands[] = new Command($name, $description, '');
                }
            }
        }

        return $commands;
    }

    static public function fromConfigArray(array $config): GistAdapter
    {
        return new self($config['username'], $config['prefix']);
    }
}
