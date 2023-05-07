<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Adapter\ManualAdapter;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Repository\Repository;
use Startwind\Forrest\Repository\RepositoryCollection;
use Symfony\Component\Yaml\Yaml;

class LocalComposerRepositoryLoader implements RepositoryLoader
{
    public const COMPOSER_FILE = 'composer.json';

    private const LOCAL_REPOSITORY_IDENTIFIER = 'local-composer';

    private const DEFAULT_LOCAL_REPOSITORY_NAME = 'Local Composer File';
    private const DEFAULT_LOCAL_DESCRIPTION = 'Commands from the local composer file.';

    /**
     * @inheritDoc
     */
    public function getIdentifiers(): array
    {
        return [self::LOCAL_REPOSITORY_IDENTIFIER];
    }

    /**
     * @inheritDoc
     */
    public function enrich(RepositoryCollection $repositoryCollection): void
    {
        $composerConfig = Yaml::parse(file_get_contents(self::COMPOSER_FILE));

        if (!array_key_exists('scripts', $composerConfig)) {
            return;
        }

        $manualAdapter = new ManualAdapter();

        foreach ($composerConfig['scripts'] as $name => $script) {
            if (is_string($script)) {
                $command = new Command($name, $script, 'composer run ' . $name);
                $manualAdapter->addCommand($command);
            }
        }

        $repository = new Repository($manualAdapter, self::DEFAULT_LOCAL_REPOSITORY_NAME, self::DEFAULT_LOCAL_DESCRIPTION, true);

        $repositoryCollection->addRepository(self::LOCAL_REPOSITORY_IDENTIFIER, $repository);
    }

}
