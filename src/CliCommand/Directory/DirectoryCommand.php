<?php

namespace Startwind\Forrest\CliCommand\Directory;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Startwind\Forrest\CliCommand\ForrestCommand;
use Symfony\Component\Yaml\Yaml;

abstract class DirectoryCommand extends ForrestCommand
{
    private const MASTER_DIRECTORY_URL = 'https://raw.githubusercontent.com/startwind/forrest-directory/main/directory.yml';

    private const MASTER_DIRECTORY_KEY = 'forrest';

    /**
     * @return array<string, mixed>
     * @throws GuzzleException
     */
    protected function getDirectories(): array
    {
        $configHandler = $this->getConfigHandler();
        $config = $configHandler->parseConfig();

        $directoryConfigs = $config->getDirectories();

        $directoryConfigs = array_merge([self::MASTER_DIRECTORY_KEY => ['url' => self::MASTER_DIRECTORY_URL]], $directoryConfigs);

        $directories = [];
        $client = new Client();

        foreach ($directoryConfigs as $key => $directoryConfig) {
            $response = $client->get(self::MASTER_DIRECTORY_URL);
            $directories[$key] = Yaml::parse($response->getBody());
        }

        return $directories;
    }
}
