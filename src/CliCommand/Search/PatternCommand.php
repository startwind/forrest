<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\CliCommand\Ai\AskCommand;
use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PatternCommand extends SearchCommand
{
    private const PERFECT_SCORE = 20;

    public const COMMAND_NAME = 'search:pattern';

    protected static $defaultName = self::COMMAND_NAME;
    protected static $defaultDescription = 'Search for commands that fit the given pattern.';

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('pattern', InputArgument::IS_ARRAY, 'The pattern you want to search for.');
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Run the command without asking for permission.');
        $this->addOption('score', 's', InputOption::VALUE_OPTIONAL, 'The minimal search score.', 10);

        $this->setAliases(['ask', 'pattern']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $minScore = $input->getOption('score');

        $this->enrichRepositories();

        $pattern = $input->getArgument('pattern');

        if (count($pattern) == 1 && str_contains($pattern[0], ' ')) {
            $pattern = explode(' ', $pattern[0]);
        }

        $this->renderInfoBox('This is a list of commands that match the given pattern. Sorted by relevance.');

        $commands = $this->getRepositoryCollection()->searchByPattern($pattern);

        $filteredCommands = [];
        $perfectCommands = [];

        foreach ($commands as $key => $command) {

            // var_dump($command->getName() . ' - ' . $command->getScore());

            if ($command->getScore() > $minScore) {
                $filteredCommands[$key] = $command;
            }

            if ($command->getScore() > self::PERFECT_SCORE) {
                $perfectCommands[$key] = $command;
            }
        }

        if (count($filteredCommands) == 0) {
            $filteredCommands = $commands;
        }

        if (count($perfectCommands) > 0) {
            $filteredCommands = $perfectCommands;
        }

        if (empty($filteredCommands)) {
            $this->renderErrorBox('No commands found that match the given pattern.');
            return Command::FAILURE;
        }

        $result = $this->runFromCommands($filteredCommands, [], true);

        if ($result !== true) {
            return $result;
        }

        $this->renderInfoBox('We are asking the Forrest AI...');

        return $this->runAiAskCommand($pattern);
    }

    /**
     * The run command can also be applied to a file. This is a shortcut for the
     * search:file symfony console command.
     */
    private function runAiAskCommand(array $patterns): int
    {
        $arguments = [
            'question' => $patterns,
            '--silent' => true
        ];

        $fileArguments = new ArrayInput($arguments);
        $fileCommand = $this->getApplication()->find(AskCommand::COMMAND_NAME);

        return $fileCommand->run($fileArguments, $this->getOutput());
    }

}
