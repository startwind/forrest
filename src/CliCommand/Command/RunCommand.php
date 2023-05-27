<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\CliCommand\Search\FileCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends CommandCommand
{
    protected static $defaultName = 'commands:run';
    protected static $defaultDescription = 'Run a specific command.';

    protected function configure(): void
    {
        $this->setAliases(['run']);
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'The commands identifier.', false);
        $this->addArgument('pattern', InputArgument::OPTIONAL, 'Small filter', false);
        $this->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Run the command without asking for permission.', false);
        $this->addOption('parameters', 'p', InputOption::VALUE_OPTIONAL, 'Parameters as json string. E.g:  -p \'{"dir_to_search_in":".", "number_on_days":"12"}\'', "{}");
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $commandIdentifier = $input->getArgument('identifier');
        $pattern = $input->getArgument('pattern');

        if (!$commandIdentifier) {
            $this->renderListCommand();
            return SymfonyCommand::SUCCESS;
        }

        if (!str_contains($commandIdentifier, ':') && file_exists($commandIdentifier)) {
            return $this->runSearchFileCommand($commandIdentifier, $pattern);
        }

        $this->enrichRepositories();

        $command = $this->getRepositoryCollection()->getCommand($commandIdentifier);

        return $this->runCommand($command, $this->extractUserParameters($input));
    }

    /**
     * The run command can also be applied to a file. This is a shortcut for the
     * search:file symfony console command.
     */
    private function runSearchFileCommand(string $filename, string $pattern): int
    {
        $arguments = [
            'filename' => $filename,
            'pattern' => $pattern
        ];

        $fileArguments = new ArrayInput($arguments);
        $fileCommand = $this->getApplication()->find(FileCommand::COMMAND_NAME);
        return $fileCommand->run($fileArguments, $this->getOutput());
    }

    /**
     * Return the parameters that are prefilled via the input option.
     */
    private function extractUserParameters(InputInterface $input): array
    {
        return json_decode($input->getOption('parameters'), true);
    }
}
