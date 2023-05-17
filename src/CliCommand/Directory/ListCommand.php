<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Startwind\Forrest\CliCommand\Directory\Exception\DirectoriesLoadException;

use Startwind\Forrest\Util\OutputHelper;
use Startwind\Forrest\Output\OutputHelper as TableOutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\TableSeparator;
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

        try {
            $directories = $this->getDirectories();
        } catch (DirectoriesLoadException $exception) {
            $directories = $exception->getDirectories();
            $messages = [];
            foreach ($exception->getExceptions() as $exception) {
                $messages[] = $exception->getMessage();
            }
            OutputHelper::writeErrorBox($output, $messages);
        }

        $repositories = [];

        foreach ($directories as $identifier => $directory) {
            if (!array_key_exists('repositories', $directory)) {
                throw new \RuntimeException('No repositories found in the given directory (' . $identifier . ').');
            }

            foreach ($directory['repositories'] as $repoIdentifier => $repository) {
                $repositories[$identifier][$repoIdentifier] = $repository;
            }

            ksort($repositories[$identifier]);
        }

        ksort($repositories);

        $rows = [];

        $all = $input->getOption('all') !== false;

        $unofficialCount = 0;

        $dirCount = 0;
        foreach ($repositories as $directoryIdentifier => $directoryRepositories) {
            foreach ($directoryRepositories as $identifier => $repository) {
                if ($all || (array_key_exists('official', $repository) && $repository['official'] === true)) {
                    $row = [
                        $identifier,
                        $repository['name'],
                        wordwrap($repository['description'], 55),
                    ];

                    if (count($directories) > 1) {
                        array_unshift($row, $directoryIdentifier);
                    }

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
            if ($dirCount != count($directories) - 1) {
                $rows[] = new TableSeparator();
            }
            $dirCount++;
        }

        $headers = ['Identifier', 'Name', 'Description', 'Installed'];

        if (count($directories) > 1) {
            array_unshift($headers, 'Directory');
        }

        if ($all) {
            $headers[] = 'Official';
        }

        TableOutputHelper::renderTable($output, $headers, $rows);

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
