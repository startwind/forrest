<?php

namespace Startwind\Forrest\CliCommand\Repository;

use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\EditableRepository;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:list';
    protected static $defaultDescription = 'List all registered command repositories.';

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->enrichRepositories();

        $rows = [];

        foreach ($this->getRepositoryCollection()->getRepositories() as $identifier => $repository) {
            $rows[] = [
                $identifier,
                $repository->getName(),
                $repository->getDescription(),
                $repository instanceof EditableRepository ? 'x' : '',
            ];
        }

        $headlines = ['Identifier', 'Name', 'Description', 'Writable'];

        OutputHelper::renderTable($output, $headlines, $rows);

        return SymfonyCommand::SUCCESS;
    }
}
