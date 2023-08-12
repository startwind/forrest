<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\CliCommand\Search\FileCommand;
use Startwind\Forrest\CliCommand\Search\PatternCommand;
use Startwind\Forrest\CliCommand\Search\ToolCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends CommandCommand
{
    public const NAME = 'commands:run';

    protected static $defaultName = self::NAME;
    protected static $defaultDescription = 'Run a specific command.';

    protected function configure(): void
    {
        parent::configure();
        $this->setAliases(['run']);
        $this->addArgument('argument', InputArgument::IS_ARRAY, 'The commands identifier.');
        // $this->addArgument('identifier', InputArgument::OPTIONAL, 'The commands identifier.', false);
        // $this->addArgument('pattern', InputArgument::OPTIONAL, 'Small filter', false);
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Run the command without asking for permission.');
        $this->addOption('parameters', 'p', InputOption::VALUE_OPTIONAL, 'Parameters as json string. E.g:  -p \'{"dir_to_search_in":".", "number_on_days":"12"}\'', "{}");
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $arguments = $input->getArgument('argument');

        if (count($arguments) == 0) {
            $this->renderListCommand();
            return SymfonyCommand::SUCCESS;
        }

        $commandName = array_pop($arguments);

        if (count($arguments) >= 1) {
            $pattern = $arguments;
        } else {
            $pattern = [];
        }

        if (!str_contains($commandName, ':')) {
            $arguments = implode(' ', $input->getArgument('argument'));

            $query = trim(implode(' ', $pattern) . ' ' . $commandName);

            if (file_exists($commandName)) {
                return $this->runSearchFileCommand($commandName, $pattern, $input->getOption('debug'));
            } elseif (!str_contains($query, ' ')) {
                return $this->runSearchToolCommand($query, $input->getOption('debug'));
            } else {
                $pattern[] = $commandName;
                return $this->runSearchPatternCommand($pattern, $input->getOption('debug'));
            }
        }

        $this->enrichRepositories();

        $command = $this->getRepositoryCollection()->getCommand($commandName);

        return $this->runCommand($command, $this->extractUserParameters($input));
    }

    /**
     * The run command can also be applied to a file. This is a shortcut for the
     * search:file symfony console command.
     */
    private function runSearchFileCommand(string $filename, array $pattern, bool $debug): int
    {
        $arguments = [
            'filename' => $filename,
            'pattern' => $pattern,
            '--debug' => $debug
        ];

        $fileArguments = new ArrayInput($arguments);
        $fileCommand = $this->getApplication()->find(FileCommand::COMMAND_NAME);

        return $fileCommand->run($fileArguments, $this->getOutput());
    }

    /**
     * The run command can also be applied to a file. This is a shortcut for the
     * search:file symfony console command.
     */
    private function runSearchToolCommand(string $tool, bool $debug): int
    {
        $arguments = [
            'tool' => $tool,
            '--debug' => $debug
        ];

        $fileArguments = new ArrayInput($arguments);
        $fileCommand = $this->getApplication()->find(ToolCommand::COMMAND_NAME);

        return $fileCommand->run($fileArguments, $this->getOutput());
    }

    /**
     * The run command can also be applied to a file. This is a shortcut for the
     * search:file symfony console command.
     */
    private function runSearchPatternCommand(array $pattern, bool $debug): int
    {
        $arguments = [
            'pattern' => $pattern,
            '--debug' => $debug
        ];

        $patternArguments = new ArrayInput($arguments);
        $command = $this->getApplication()->find(PatternCommand::COMMAND_NAME);
        return $command->run($patternArguments, $this->getOutput());
    }

    /**
     * Return the parameters that are prefilled via the input option.
     */
    private function extractUserParameters(InputInterface $input): array
    {
        return json_decode($input->getOption('parameters'), true);
    }
}
