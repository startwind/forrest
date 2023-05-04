<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\FileParameter;
use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileCommand extends SearchCommand
{
    protected static $defaultName = 'search:file';
    protected static $defaultDescription = 'Search for commands that fit the given file.';

    protected function configure()
    {
        $this->addArgument('filename', InputArgument::REQUIRED, 'The filename you want to get commands for.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $filename = $input->getArgument('filename');

        if (!file_exists($filename)) {
            $this->writeWarning($output, 'File not found.');
            return SymfonyCommand::FAILURE;
        }

        $this->writeInfo($output, 'This is a list of commands that are applicable to the given file or file type.');

        $fileCommands = $this->search(function (Command $command, $config) {
            $parameters = $command->getParameters();
            foreach ($parameters as $parameter) {
                if ($parameter instanceof FileParameter) {
                    $fileTypes = $parameter->getFileFormats();
                    foreach ($fileTypes as $fileType) {
                        if (str_contains($config['filename'], $fileType)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }, ['filename' => $filename]);

        if (!empty($fileCommands)) {
            OutputHelper::renderCommands($output, $fileCommands);
        } else {
            $this->writeWarning($output, 'No commands found that match this file type.');
        }

        $output->writeln('');

        return SymfonyCommand::SUCCESS;
    }
}
