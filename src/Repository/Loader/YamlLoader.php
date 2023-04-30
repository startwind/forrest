<?php

namespace Startwind\Forrest\Repository\Loader;

use GuzzleHttp\Client;
use Startwind\Forrest\Adapter\AdapterFactory;
use Startwind\Forrest\Repository\Repository;
use Startwind\Forrest\Repository\RepositoryCollection;
use Symfony\Component\Yaml\Yaml;

class YamlLoader
{
    const CONFIG_ELEMENT_REPOSITORIES = 'repositories';
    const CONFIG_ELEMENT_ADAPTER = 'adapter';
    const CONFIG_ELEMENT_NAME = 'name';

    const CONFIG_ELEMENT_DESCRIPTION = 'description';


    private array $config;

    private array $repositories = [];

    public function __construct(string $yamlFilename, Client $client)
    {
        if (!file_exists($yamlFilename)) {
            throw new \RuntimeException("Config file ($yamlFilename) not found");
        }

        $this->config = Yaml::parse(file_get_contents($yamlFilename));

        if (!array_key_exists(self::CONFIG_ELEMENT_REPOSITORIES, $this->config)) {
            throw new \RuntimeException('Config file does not contain the mandatory element "' . self::CONFIG_ELEMENT_REPOSITORIES . '".');
        }

        foreach ($this->config[self::CONFIG_ELEMENT_REPOSITORIES] as $repoName => $repoConfig) {
            $adapterIdentifier = $repoConfig[self::CONFIG_ELEMENT_ADAPTER];

            $adapter = AdapterFactory::getAdapter($adapterIdentifier, $repoConfig['config'], $client);

            if (!array_key_exists(self::CONFIG_ELEMENT_NAME, $repoConfig)) {
                throw new \RuntimeException('No field for repository "' . $repoName . '" with value ' . self::CONFIG_ELEMENT_NAME . ' found. Fields given are: ' . implode(', ', array_keys($repoConfig)) . '.');
            }

            if (!array_key_exists(self::CONFIG_ELEMENT_DESCRIPTION, $repoConfig)) {
                throw new \RuntimeException('No field for repository "' . $repoName . '" with value ' . self::CONFIG_ELEMENT_DESCRIPTION . ' found. Fields given are: ' . implode(', ', array_keys($repoConfig)) . '.');
            }

            $this->repositories[$repoName] = new Repository($adapter, $repoConfig['name'], $repoConfig['description']);
        }
    }

    public function enrich(RepositoryCollection $repositoryCollection): void
    {
        foreach ($this->repositories as $repoIdentifier => $repository) {
            $repositoryCollection->addRepository($repoIdentifier, $repository);
        }

    }
}
