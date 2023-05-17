<?php

namespace Startwind\Forrest\CliCommand\Directory;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Startwind\Forrest\Adapter\Loader\HttpAwareLoader;
use Startwind\Forrest\Adapter\Loader\LoaderFactory;
use Startwind\Forrest\CliCommand\Directory\Exception\DirectoriesLoadException;
use Startwind\Forrest\CliCommand\ForrestCommand;
use Symfony\Component\Yaml\Yaml;

abstract class DirectoryCommand extends ForrestCommand
{
    private const MASTER_DIRECTORY_URL = 'https://raw.githubusercontent.com/startwind/forrest-directory/main/directory.yml';

    private const MASTER_DIRECTORY_KEY = 'forrest';

    /**
     * @return array<string, mixed>
     * @throws \Startwind\Forrest\CliCommand\Directory\Exception\DirectoriesLoadException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getDirectories(): array
    {
        $directoryConfigs = $this->getDirectoryConfigs();

        $directories = [];
        $client = new Client();

        $directoriesLoadException = new DirectoriesLoadException();

        foreach ($directoryConfigs as $key => $directoryConfig) {
            if (array_key_exists('url', $directoryConfig)) {
                $response = $client->get($directoryConfig['url']);
                $directories[$key] = Yaml::parse($response->getBody());
            } elseif (array_key_exists('loader', $directoryConfig)) {
                $loader = LoaderFactory::create($directoryConfig['loader']);
                if ($loader instanceof HttpAwareLoader) {
                    $loader->setClient($client);
                }
                try {
                    $content = $loader->load();
                } catch (\Exception $exception) {
                    $directoriesLoadException->addException(new \RuntimeException('Directory error (' . $key . '): ' . $exception->getMessage()));
                    continue;
                }
                $directories[$key] = Yaml::parse($content);
            } else {
                throw new \RuntimeException('The directory configuration needs to have an url or loader defined.');
            }
        }

        if ($directoriesLoadException->hasExceptions()) {
            $directoriesLoadException->setDirectories($directories);
            throw $directoriesLoadException;
        }

        return $directories;
    }

    protected function getDirectoryConfigs(): array
    {
        $configHandler = $this->getConfigHandler();
        $config = $configHandler->parseConfig();

        $directoryConfigs = $config->getDirectories();

        return array_merge([self::MASTER_DIRECTORY_KEY => ['url' => self::MASTER_DIRECTORY_URL]], $directoryConfigs);
    }
}
