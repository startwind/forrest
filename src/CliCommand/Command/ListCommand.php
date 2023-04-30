<?php

namespace Startwind\Forrest\CliCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends CommandCommand
{
    protected static $defaultName = 'commands:gist:list';
    protected static $defaultDescription = 'List all command that are registered in the repositories.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initRepositories();

        $rows = [];

        foreach ($this->getRepositoryCollection()->getRepositories() as $repoIdentifier => $repository) {
            $commands = $repository->getCommands();
            foreach ($commands as $command) {
                $rows[] = [
                    $repoIdentifier . ':' . $command->getName(),
                    $command->getDescription()
                ];
            }
        }

        $this->renderTable($output, ['Command', 'Description'], $rows);

        return SymfonyCommand::SUCCESS;
    }
}
