<?php

namespace Startwind\Forrest\Adapter\Loader;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Startwind\Forrest\Adapter\Exception\RepositoryNotFoundException;
use Startwind\Forrest\Adapter\Exception\UnableToFetchRepositoryException;

class HttpFileLoader implements Loader, HttpAwareLoader, CachableLoader
{
    private static $offline = false;

    private string $filename;
    private ?Client $client;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @inheritDoc
     */
    public function load(): string
    {
        try {
            $response = $this->client->get($this->filename);
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new RepositoryNotFoundException("The given repository (" . $this->filename . ") can't be found.");
            } else {
                throw $exception;
            }
        } catch (ServerException $exception) {
            throw new UnableToFetchRepositoryException('Unable to fetch data from repository due to server errors.');
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

    /**
     * @inheritDoc
     */
    public static function fromConfigArray(array $config): Loader
    {
        return new self($config['file']);
    }

    public function assertHealth(): void
    {
        if (self::$offline) {
            throw new \RuntimeException('Cannot connect to the internet. Please check if your computer is online.');
        }

        try {
            $this->client->get('https://www.google.com');
        } catch (\Exception $exception) {
            self::$offline = true;
            throw new \RuntimeException('Cannot connect to the internet. Please check if your computer is online.');
        }
    }

    public function getCacheKey(): string
    {
        return md5($this->filename);
    }
}
