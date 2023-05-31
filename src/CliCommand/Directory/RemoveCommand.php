<?php

namespace Startwind\Forrest\CliCommand\Directory;

use GuzzleHttp\Client;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class RemoveCommand extends DirectoryCommand
{
    public const COMMAND_NAME = 'directory:remove';

    protected static $defaultName = self::COMMAND_NAME;
    protected static $defaultDescription = 'Remove an external directory from the list.';

    protected function configure()
    {
        parent::configure();
        $this->addArgument('directory', InputArgument::OPTIONAL, 'The config string for the directory.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $selectedDirectoryIdentifier = $input->getArgument('directory');

        $directories = $this->getDirectoryConfigs();
        $removableDirectories = $directories;

        if ($selectedDirectoryIdentifier == DirectoryCommand::MASTER_DIRECTORY_KEY) {
            OutputHelper::writeErrorBox($output, 'You are not allowed to remove the Forrest master directory.');
            return SymfonyCommand::FAILURE;
        }

        unset($removableDirectories[DirectoryCommand::MASTER_DIRECTORY_KEY]);

        if (!$selectedDirectoryIdentifier) {
            /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHandler */
            $questionHandler = $this->getHelper('question');
            $selectedDirectoryIdentifier = $questionHandler->ask($input, $output, new ChoiceQuestion('Which directory do you want to export? ', array_keys($removableDirectories)));
        }

        if (!array_key_exists($selectedDirectoryIdentifier, $directories)) {
            OutputHelper::writeErrorBox($output, 'No directory with identifier "' . $selectedDirectoryIdentifier . '" found.');
            return SymfonyCommand::FAILURE;
        }

        unset($directories[$selectedDirectoryIdentifier]);

        $config = $this->getConfigHandler()->parseConfig();

        $config->setDirectories($directories);

        $this->getConfigHandler()->dumpConfig($config);

        OutputHelper::writeInfoBox($output, "Successfully removed directory with identifier " . $selectedDirectoryIdentifier . ".");

        return SymfonyCommand::SUCCESS;
    }
}
