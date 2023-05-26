<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Adapter\Loader\LocalFileLoader;
use Startwind\Forrest\Adapter\YamlAdapter;
use Startwind\Forrest\Repository\FileRepository;
use Startwind\Forrest\Repository\RepositoryCollection;
use Symfony\Component\Yaml\Yaml;

class LocalRepositoryLoader implements RepositoryLoader
{
    public const LOCAL_REPOSITORY_IDENTIFIER = 'local';

    public const DEFAULT_LOCAL_REPOSITORY_NAME = 'Local repository';
    public const DEFAULT_LOCAL_DESCRIPTION = 'this is a local repository';

    private const FIELD_NAME = 'name';
    private const FIELD_IDENTIFIER = 'identifier';
    private const FIELD_DESCRIPTION = 'description';

    private string $localCommandsFile;

    public string $description;

    private string $name;

    private string $identifier;

    public function __construct(string $localCommandsFile)
    {
        $this->localCommandsFile = $localCommandsFile;

        $config = Yaml::parse(file_get_contents($localCommandsFile));

        if (array_key_exists('repository', $config)) {
            if (array_key_exists(self::FIELD_NAME, $config['repository'])) {
                $this->name = $config['repository'][self::FIELD_NAME];
            } else {
                $this->name = self::DEFAULT_LOCAL_REPOSITORY_NAME;
            }

            if (array_key_exists(self::FIELD_DESCRIPTION, $config['repository'])) {
                $this->description = $config['repository'][self::FIELD_DESCRIPTION] . ' (' . self::DEFAULT_LOCAL_DESCRIPTION  . ')';
            } else {
                $this->description = ucfirst(self::DEFAULT_LOCAL_DESCRIPTION);
            }

            if (array_key_exists(self::FIELD_IDENTIFIER, $config['repository'])) {
                $this->identifier = $config['repository'][self::FIELD_IDENTIFIER];
            } else {
                $this->identifier = self::LOCAL_REPOSITORY_IDENTIFIER;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getIdentifiers(): array
    {
        return [$this->identifier];
    }

    /**
     * @inheritDoc
     */
    public function enrich(RepositoryCollection $repositoryCollection)
    {
        $adapter = new YamlAdapter(new LocalFileLoader($this->localCommandsFile));

        $repository = new FileRepository($adapter, $this->name, $this->description, true);

        $repositoryCollection->addRepository($this->identifier, $repository);
    }

}
