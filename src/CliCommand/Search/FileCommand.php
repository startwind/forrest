<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\Command\Parameters\FileParameter;
use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\Repository;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileCommand extends ForrestCommand
{
    protected static $defaultName = 'search:file';
    protected static $defaultDescription = 'List all repositories in the official Forrest directory.';

    protected function configure()
    {
        $this->addArgument('filename', InputArgument::REQUIRED, 'The filename you want to get commands for.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $filename = $input->getArgument('filename');

        $repositories = $this->getRepositoryCollection()->getRepositories();

        $fileCommands = [];

        $this->writeInfo($output, 'This is a list of commands that are applicable to the given file or file type.');

        foreach ($repositories as $repositoryId => $repository) {
            $commands = $repository->getCommands();
            foreach ($commands as $command) {
                $parameters = $command->getParameters();
                foreach ($parameters as $parameter) {
                    if ($parameter instanceof FileParameter) {
                        $fileTypes = $parameter->getFileFormats();
                        foreach ($fileTypes as $fileType) {
                            if (str_contains($filename, $fileType)) {
                                $fileCommands[Repository::createUniqueCommandName($repositoryId, $command)] = $command;
                            }
                        }
                    }
                }
            }
        }

        OutputHelper::renderCommands($output, $fileCommands);

        $output->writeln('');

        return SymfonyCommand::SUCCESS;
    }
}
