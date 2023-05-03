<?php

namespace Startwind\Forrest\CliCommand\Repository;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\Config\ConfigFileHandler;

abstract class RepositoryCommand extends ForrestCommand
{
    /**
     * Normalize the repository name to fit as an identifier.
     */
    protected function getIdentifierSuggestion(string $name): string
    {
        return strtolower(str_replace(' ', '-', $name));
    }

    /**
     * Register a new repository
     */
    protected function registerRepository(string $identifier, string $name, string $description, string $repositoryFileName)
    {
        $repoArray = [
            'adapter' => 'yaml',
            'name' => $name,
            'description' => $description,
            'config' => [
                'file' => $repositoryFileName
            ]
        ];

        $configHandler = $this->getConfigHandler();

        $config = $configHandler->parseConfig();
        $config->addRepository($identifier, $repoArray);
        $configHandler->dumpConfig($config);
    }
}
