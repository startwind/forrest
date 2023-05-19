<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\CliCommand\Search\FileCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
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
        if (!$input->getArgument('identifier')) {
            $this->renderListCommand();
            return SymfonyCommand::SUCCESS;
        }

        $commandIdentifier = $input->getArgument('identifier');

        if (!str_contains($commandIdentifier, ':') && file_exists($commandIdentifier)) {
            $arguments = [
                'filename' => $commandIdentifier,
                'pattern' => $input->getArgument('pattern')
            ];

            $fileArguments = new ArrayInput($arguments);
            $fileCommand = $this->getApplication()->find(FileCommand::COMMAND_NAME);
            return $fileCommand->run($fileArguments, $output);
        }

        $this->enrichRepositories();

        $userParameters = json_decode($input->getOption('parameters'), true);


        return $this->runCommand($commandIdentifier, $userParameters);
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('identifier')) {
            $suggestions->suggestValues(['linux:run', 'linux:delete']);
        }
    }
}
