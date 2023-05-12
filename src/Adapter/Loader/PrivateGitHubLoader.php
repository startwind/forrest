<?php

namespace Startwind\Forrest\Adapter\Loader;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class PrivateGitHubLoader implements Loader, HttpAwareLoader
{
    private string $file;
    private string $token;

    private ?Client $client;
    private string $user;
    private string $repository;

    public function __construct(string $user, string $repository, string $file, string $token)
    {
        $this->file = $file;
        $this->token = $token;
        $this->repository = $repository;
        $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    public static function fromConfigArray(array $config): Loader
    {
        if (!array_key_exists('user', $config)) {
            throw new \RuntimeException('Missing config field "name" missing.');
        }

        if (!array_key_exists('repository', $config)) {
            throw new \RuntimeException('Missing config field "repository" missing.');
        }

        if (!array_key_exists('file', $config)) {
            throw new \RuntimeException('Missing config field "file" missing.');
        }

        if (!array_key_exists('token', $config)) {
            throw new \RuntimeException('Missing config field "token" missing.');
        }

        return new self($config['user'], $config['repository'], $config['file'], $config['token']);
    }

    /**
     * @inheritDoc
     */
    public function load(): string
    {
        // curl -H "Authorization: token DEIN_TOKEN_HIER" -H 'Accept: application/vnd.github.v3.raw' -L https://api.github.com/repos/USER/REPO/contents/PATH_TO_FILE
        $url = sprintf('https://api.github.com/repos/%s/%s/contents/%s', $this->user, $this->repository, $this->file);

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => ' token ' . $this->token,
                    'Accept' => 'application/vnd.github.v3.raw'
                ]
            ]);
        } catch (ClientException $exception) {
            if (str_contains($exception->getMessage(), 'Bad credentials')) {
                throw new \RuntimeException('Forbidden due to bad credentials. Please check your token.');
            } elseif (str_contains($exception->getMessage(), 'Not Found')) {
                throw new \RuntimeException('File was not found. Please check the repository and file in your config.');
            } else {
                throw $exception;
            }
        }

        return (string)$response->getBody();
    }

    /**
     * @inheritDoc
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
