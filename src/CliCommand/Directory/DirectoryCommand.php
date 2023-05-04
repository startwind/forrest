<?php

namespace Startwind\Forrest\CliCommand\Directory;

use GuzzleHttp\Client;
use Startwind\Forrest\CliCommand\ForrestCommand;
use Symfony\Component\Yaml\Yaml;

abstract class DirectoryCommand extends ForrestCommand
{
    public const MASTER_DIRECTORY_URL = 'https://raw.githubusercontent.com/startwind/forrest-directory/main/directory.yml';

    protected function getDirectory(): array
    {
        $client = new Client();
        $response = $client->get(self::MASTER_DIRECTORY_URL);
        $directory = Yaml::parse($response->getBody());

        return $directory;
    }
}
