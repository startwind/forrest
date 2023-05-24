<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\CliCommand\RunCommand;
use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\Repository;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class SearchCommand extends RunCommand
{
    /**
     * Search for commands that match a given pattern. The pattern is defined in the
     * callable function that has to be injected.
     *
     * @return \Startwind\Forrest\Command\Command[]
     * @throws \Exception
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

    /**
     * Choose from a list of commands and execute one.
     */
    protected function runFromCommands(array $commands, $values = []): int
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $command = OutputHelper::renderCommands(
            $this->getOutput(),
            $this->getInput(),
            $questionHelper,
            $commands,
            null,
            -1,
            true
        );

        if ($command === false) {
            return SymfonyCommand::FAILURE;
        }

        $this->getOutput()->writeln('');

        return $this->runCommand($command->getFullyQualifiedIdentifier(), $values);
    }
}
