<?php

namespace Startwind\Forrest\CliCommand\Repository;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:list';
    protected static $defaultDescription = 'List all registered command repositories.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initRepositories();

        $rows = [];

        foreach ($this->getRepositoryCollection()->getRepositories() as $repository) {
            $rows[] = [
                $repository->getName(),
                $repository->getDescription(),
                $repository->getAdapter()->getType()
            ];
        }

        $headlines = ['Name', 'Description', 'Type'];

        $this->renderTable($output, $headlines, $rows);

        return SymfonyCommand::SUCCESS;
    }


    private function geConfig(): array
    {
        return Yaml::parse(file_get_contents(self::DEFAULT_CONFIG_FILE), true);
    }
}
