<?php

namespace Startwind\Forrest\Adapter\Loader;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

/**
 * @see https://docs.github.com/de/rest/repos/contents?apiVersion=2022-11-28#create-a-file
 */
class PrivateGitHubLoader implements Loader, HttpAwareLoader, WritableLoader
{
    private const GIT_HUB_API_ENDPOINT = 'https://api.github.com/repos/%s/%s/contents/%s';

    private string $file;
    private string $token;

    private ?Client $client = null;
    private string $user;
    private string $repository;

    private array $fileInformation = [];

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

    private function getHeaders(): array
    {
        return [
            'Authorization' => ' token ' . $this->token,
            'Accept' => 'application/vnd.github+json'
        ];
    }

    private function getUrl(): string
    {
        return sprintf(self::GIT_HUB_API_ENDPOINT, $this->user, $this->repository, $this->file);
    }

    /**
     * @inheritDoc
     */
    public function load(): string
    {
        if (is_null($this->client)) {
            throw  new \RuntimeException('The client for this loader was not set but is needed. Please call setClient() before the load() function.');
        }

        if (!empty($this->fileInformation)) {
            return base64_decode($this->fileInformation['content']);
        }

        try {
            $response = $this->client->request('GET', $this->getUrl(), [
                'headers' => $this->getHeaders()
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

        $this->fileInformation = json_decode((string)$response->getBody(), true);

        return base64_decode($this->fileInformation['content']);
    }

    /**
     * @inheritDoc
     */
    public function write(string $content)
    {
        $this->load();
        $this->client->request('PUT', $this->getUrl(), [
            'headers' => $this->getHeaders(),
            RequestOptions::JSON => [
                'message' => 'updated repository via Forrest CLI',
                'content' => base64_encode($content),
                'sha' => $this->fileInformation['sha']
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
