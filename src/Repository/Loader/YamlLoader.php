<?php

namespace Startwind\Forrest\Repository\Loader;

use GuzzleHttp\Client;
use Startwind\Forrest\Adapter\AdapterFactory;
use Startwind\Forrest\Adapter\EditableAdapter;
use Startwind\Forrest\Logger\ForrestLogger;
use Startwind\Forrest\Repository\Api\ApiRepository;
use Startwind\Forrest\Repository\Api\EditableApiRepository;
use Startwind\Forrest\Repository\File\EditableFileRepository;
use Startwind\Forrest\Repository\File\FileRepository;
use Startwind\Forrest\Repository\Repository;
use Startwind\Forrest\Repository\RepositoryCollection;
use Symfony\Component\Yaml\Yaml;

class YamlLoader implements RepositoryLoader
{
    public const CONFIG_ELEMENT_REPOSITORIES = 'repositories';
    public const CONFIG_ELEMENT_ADAPTER = 'adapter';
    public const CONFIG_ELEMENT_NAME = 'name';
    public const CONFIG_ELEMENT_CONFIG = 'config';

    public const CONFIG_ELEMENT_DESCRIPTION = 'description';

    private array $config;

    private array $repositories = [];

    public function __construct(string $userConfigFile, string $fallbackConfigFile, private readonly Client $client)
    {
        if (!file_exists($userConfigFile)) {
            $configFile = $fallbackConfigFile;
        } else {
            $configFile = $userConfigFile;
        }

        if (!file_exists($configFile)) {
            throw new \RuntimeException("Config file ($configFile) not found");
        }

        try {
            $this->config = Yaml::parse(file_get_contents($configFile));
        } catch (\Exception $exception) {
            throw new \RuntimeException('Unable to load YAML file ("' . $configFile . '"): ' . $exception->getMessage());
        }

        if (!array_key_exists(self::CONFIG_ELEMENT_REPOSITORIES, $this->config)) {
            throw new \RuntimeException('Config file does not contain the mandatory element "' . self::CONFIG_ELEMENT_REPOSITORIES . '".');
        }
    }

    private function initRepositories(): void
    {
        foreach ($this->config[self::CONFIG_ELEMENT_REPOSITORIES] as $repoName => $repoConfig) {

            if (!array_key_exists('type', $repoConfig)) {
                $repoType = Repository::TYPE_FILE;
            } else {
                $repoType = $repoConfig['type'];
            }

            /** @todo these three checks can be condensed */
            if (!array_key_exists(self::CONFIG_ELEMENT_NAME, $repoConfig)) {
                throw new \RuntimeException('No field for repository "' . $repoName . '" with value ' . self::CONFIG_ELEMENT_NAME . ' found. Fields given are: ' . implode(', ', array_keys($repoConfig)) . '.');
            }

            if (!array_key_exists(self::CONFIG_ELEMENT_DESCRIPTION, $repoConfig)) {
                throw new \RuntimeException('No field for repository "' . $repoName . '" with value ' . self::CONFIG_ELEMENT_DESCRIPTION . ' found. Fields given are: ' . implode(', ', array_keys($repoConfig)) . '.');
            }

            if (!array_key_exists(self::CONFIG_ELEMENT_CONFIG, $repoConfig)) {
                throw new \RuntimeException('No field for repository "' . $repoName . '" with value ' . self::CONFIG_ELEMENT_CONFIG . ' found. Fields given are: ' . implode(', ', array_keys($repoConfig)) . '.');
            }

            // @todo this should be moved to a repository factory

            if ($repoType == Repository::TYPE_API) {
                if (array_key_exists('password', $repoConfig['config'])) {
                    $newRepo = new EditableApiRepository($repoConfig['config']['endpoint'], $repoConfig['name'], $repoConfig['description'], $this->client);
                    $newRepo->setPassword($repoConfig['config']['password']);
                } else {
                    $newRepo = new ApiRepository($repoConfig['config']['endpoint'], $repoConfig['name'], $repoConfig['description'], $this->client);
                }
            } else {
                $adapterIdentifier = $repoConfig[self::CONFIG_ELEMENT_ADAPTER];
                $adapter = AdapterFactory::getAdapter($adapterIdentifier, $repoConfig['config'], $this->client);
                if ($adapter instanceof EditableAdapter && $adapter->isEditable()) {
                    $newRepo = new EditableFileRepository($adapter, $repoConfig['name'], $repoConfig['description']);
                } else {
                    $newRepo = new FileRepository($adapter, $repoConfig['name'], $repoConfig['description']);
                }
            }

            try {
                $newRepo->assertHealth();
            } catch (\Exception $exception) {
                ForrestLogger::error("Unable to load repository " . $repoName . ": " . $exception->getMessage() . '.');
                continue;
            }

            $this->repositories[$repoName] = $newRepo;
        }
    }

    /**
     * @inheritDoc
     */
    public function getIdentifiers(): array
    {
        return array_keys($this->config[self::CONFIG_ELEMENT_REPOSITORIES]);
    }

    /**
     * @inheritDoc
     */
    public function enrich(RepositoryCollection $repositoryCollection): void
    {
        $this->initRepositories();

        foreach ($this->repositories as $repoIdentifier => $repository) {
            $repositoryCollection->addRepository($repoIdentifier, $repository);
        }
    }
}
