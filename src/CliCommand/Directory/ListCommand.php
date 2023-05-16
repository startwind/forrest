<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends DirectoryCommand
{
    protected static $defaultName = 'directory:list';
    protected static $defaultDescription = 'List all repositories in the official Forrest directory.';

    protected function configure()
    {
        // @todo as long as there are only a few repositories listed we show all. This mechanism should be activated
        //       as soon as there a longer list.
        $this->addOption('all', '', InputOption::VALUE_OPTIONAL, 'List all repositories. Default is that only official repositories are shown.', true);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->initRepositoryLoader();
        $activeRepositories = $this->getRepositoryLoader()->getIdentifiers();

        $directories = $this->getDirectories();

        $repositories = [];

        foreach ($directories as $directory) {
            $repositories = array_merge($repositories, $directory['repositories']);
        }

        ksort($repositories);

        $rows = [];

        $all = $input->getOption('all') !== false;

        $unofficialCount = 0;

        foreach ($repositories as $identifier => $repository) {

            if ($all || (array_key_exists('official', $repository) && $repository['official'] === true)) {
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

                if ($all) {
                    if (array_key_exists('official', $repository) && $repository['official'] === true) {
                        $row[] = 'x';
                    } else {
                        $row[] = '';
                    }
                }

                $rows [] = $row;
            } else {
                $unofficialCount++;
            }
        }

        $headers = ['Identifier', 'Name', 'Description', 'Installed'];

        if ($all) {
            $headers[] = 'Official';
        }

        OutputHelper::renderTable($output, $headers, $rows);

        if ($unofficialCount > 0) {
            $output->writeln([
                    '',
                    'This list only contains official repositories. If you also want to see',
                    'the other ' . $unofficialCount . ' unofficial repositories please use the --all option.'
                ]
            );
        }

        return SymfonyCommand::SUCCESS;
    }
}
