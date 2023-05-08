<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Adapter\ManualAdapter;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Prompt;
use Startwind\Forrest\Repository\Repository;
use Startwind\Forrest\Repository\RepositoryCollection;
use Symfony\Component\Yaml\Yaml;

abstract class LocalJsonRepositoryLoader implements RepositoryLoader
{
    /**
     * Handle the loading of a local json file.
     */
    protected function enrichWithFile(RepositoryCollection $repositoryCollection, string $fileName, string $commandPrefix, string $repositoryName, string $repositoryDescription, string $repositoryInventory): void
    {
        $jsonConfig = Yaml::parse(file_get_contents($fileName));

        if (!array_key_exists('scripts', $jsonConfig)) {
            return;
        }

        $manualAdapter = new ManualAdapter();

        foreach ($jsonConfig['scripts'] as $name => $script) {
            if (is_string($script)) {
                $command = new Command($name, $script, new Prompt($commandPrefix . $name));
                $manualAdapter->addCommand($command);
            }
        }

        $repository = new Repository($manualAdapter, $repositoryName, $repositoryDescription, true);

        $repositoryCollection->addRepository($repositoryInventory, $repository);
    }
}
