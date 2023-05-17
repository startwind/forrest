<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ExportCommand extends DirectoryCommand
{
    protected static $defaultName = 'directory:export';
    protected static $defaultDescription = 'Add an external directory to the list.';

    protected function configure()
    {
        $this->addArgument('directory', InputArgument::OPTIONAL, 'The config string for the directory.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $selectedDirectoryIdentifier = $input->getArgument('directory');

        $directories = $this->getDirectoryConfigs();

        if (!$selectedDirectoryIdentifier) {
            $questionHandler = $this->getHelper('question');
            $selectedDirectoryIdentifier = $questionHandler->ask($input, $output, new ChoiceQuestion('Which directory do you want to export? ', array_keys($directories)));
        }

        $selectedDirectory = $directories[$selectedDirectoryIdentifier];

        OutputHelper::writeInfoBox($output, 'To import this repository on another machine please use this command:');

        $output->writeln('  forrest ' . ImportCommand::COMMAND_NAME . ' ' . escapeshellarg($selectedDirectoryIdentifier) . ' ' . escapeshellarg(json_encode($selectedDirectory)));
        $output->writeln('');

        return SymfonyCommand::SUCCESS;
    }
}
