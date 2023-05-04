<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\Repository\Repository;

abstract class SearchCommand extends ForrestCommand
{
    /**
     * Search for commands that match a given pattern. The pattern is defined in the
     * callable function that has to be injected.
     */
    protected function search(callable $finder, array $config): array
    {
        $repositories = $this->getRepositoryCollection()->getRepositories();

        $foundCommands = [];

        foreach ($repositories as $repositoryId => $repository) {
            $commands = $repository->getCommands();
            foreach ($commands as $command) {
                if ($finder($command, $config)) {
                    $foundCommands[Repository::createUniqueCommandName($repositoryId, $command)] = $command;
                }
            }
        }

        return $foundCommands;
    }
}
