<?php

namespace Startwind\Forrest\CliCommand\Directory;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Startwind\Forrest\CliCommand\ForrestCommand;
use Symfony\Component\Yaml\Yaml;

abstract class DirectoryCommand extends ForrestCommand
{
    public const MASTER_DIRECTORY_URL = 'https://raw.githubusercontent.com/startwind/forrest-directory/main/directory.yml';

    /**
     * @return array<string, mixed>
     * @throws GuzzleException
     */
    protected function getDirectory(): array
    {
        $client = new Client();
        $response = $client->get(self::MASTER_DIRECTORY_URL);
        return Yaml::parse($response->getBody());
    }
}
