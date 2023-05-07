<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends DirectoryCommand
{
    protected static $defaultName = 'directory:list';
    protected static $defaultDescription = 'List all repositories in the official Forrest directory.';

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->initYamlLoader();
        $activeRepositories = $this->getYamlLoader()->getIdentifiers();

        $directory = $this->getDirectory();

        $rows = [];

        foreach ($directory['repositories'] as $identifier => $repository) {
            $row = [
                $identifier,
                $repository['name'],
                $repository['description'],
            ];

            if (in_array($identifier, $activeRepositories)) {
                $row[] = 'x';
            } else {
                $row[] = '';
            }

            $rows [] = $row;
        }

        OutputHelper::renderTable($output, ['Identifier', 'Name', 'Description', 'Installed'], $rows);

        return SymfonyCommand::SUCCESS;
    }
}
