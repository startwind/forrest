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

        $configHandler = new ConfigFileHandler($this->getUserConfigFile());
        $config = $configHandler->parse();
        $config->addRepository($identifier, $repoArray);
        $configHandler->dump($config);
    }
}
