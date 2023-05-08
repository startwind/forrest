<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\Parameter;
use Startwind\Forrest\Command\Prompt;
use Startwind\Forrest\Config\RecentParameterMemory;
use Startwind\Forrest\Runner\CommandRunner;
use Startwind\Forrest\Runner\Exception\ToolNotFoundException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class RunCommand extends CommandCommand
{
    protected static $defaultName = 'commands:run';
    protected static $defaultDescription = 'Run a specific command.';

    protected function configure(): void
    {
        $this->setAliases(['run']);
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'The commands identifier.', false);
        $this->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Run the command without asking for permission.', false);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getArgument('identifier')) {
            $this->renderListCommand();
            return SymfonyCommand::SUCCESS;
        }

        $this->enrichRepositories();

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $commandIdentifier = $input->getArgument('identifier');
        $command = $this->getCommand($commandIdentifier);
        $repositoryIdentifier = $this->getRepositoryIdentifier($commandIdentifier);

        $this->showCommandInformation($output, $command);

        // $this->handleRootUser($output);

        $parameters = $command->getParameters();

        $values = $this->handleParameters($questionHelper, $commandIdentifier, $parameters);

        $prompt = $command->getPrompt($values);

        if (count($values) > 0) {
            $output->writeln('');
            $output->writeln('  Final prompt: ');
            $this->renderInfoBox($prompt);
        }

        if (!$command->isRunnable()) {
            $this->renderWarningBox([
                'This command was marked as not runnable from Forrest. Please copy the prompt and run it',
                'on the command line.'
            ]);
            return SymfonyCommand::SUCCESS;
        }

        if ($input->getOption('force') !== false) {
            if (!$this->handleChecksum($command, $repositoryIdentifier, $questionHelper, $input, $output)) {
                return SymfonyCommand::FAILURE;
            }
        } else {
            if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('  Are you sure you want to run that command? [y/n] ', false))) {
                return SymfonyCommand::FAILURE;
            }
        }

        $output->writeln('');

        try {
            $this->executeCommand($output, $prompt);
        } catch (ToolNotFoundException $exception) {
            $this->renderErrorBox($exception->getMessage());
            return SymfonyCommand::FAILURE;
        }

        $this->getConfigHandler()->persistChecksum($command, $repositoryIdentifier);
        $this->getRecentParameterMemory()->dump();

        return SymfonyCommand::SUCCESS;
    }

    /**
     * @param Parameter[] $parameters
     * @return array<string, mixed>
     */
    private function handleParameters(QuestionHelper $questionHelper, string $commandIdentifier, array $parameters): array
    {
        $input = $this->getInput();
        $output = $this->getOutput();

        $values = [];

        $memory = $this->getRecentParameterMemory();

        foreach ($parameters as $identifier => $parameter) {

            $fullParameterIdentifier = $commandIdentifier . ':' . $identifier;

            $additional = $this->getAdditionalInfo($memory, $fullParameterIdentifier, $parameter);

            if ($parameter->getName()) {
                $name = $identifier . ' (' . $parameter->getName() . ')';
            } else {
                $name = $identifier;
            }

            if ($parameter->hasValues()) {
                $values[$identifier] = $questionHelper->ask($input, $output, new ChoiceQuestion('  Select value for ' . $name . $additional['string'] . ': ', $parameter->getValues()));
            } else {
                $question = new Question('  Select value for ' . $name . $additional['string'] . ': ', $additional['value']);

                if (
                    str_contains('password', strtolower($parameter->getName())) ||
                    str_contains('secret', strtolower($parameter->getName()))
                ) {
                    $question->setHidden(true);
                    $question->setHiddenFallback(false);
                }

                $values[$identifier] = $questionHelper->ask($input, $output, $question);
            }

            if ($values[$identifier]) {
                $memory->addParameter($fullParameterIdentifier, $values[$identifier]);
            }
        }

        return $values;
    }

    /**
     * Handle default and recent values for the current parameter.
     */
    private function getAdditionalInfo(RecentParameterMemory $memory, string $fullParameterIdentifier, Parameter $parameter): array
    {
        if ($memory->hasParameter($fullParameterIdentifier)) {
            $recentValue = $memory->getParameter($fullParameterIdentifier);
        } else {
            $recentValue = '';
        }

        if ($parameter->getDefaultValue()) {
            if ($recentValue != '' && $recentValue != $parameter->getDefaultValue()) {
                $recentOutput = ', recent: ' . $recentValue;
            } else {
                $recentOutput = '';
            }
            $defaultString = ' [default: ' . $parameter->getDefaultValue() . $recentOutput . ']';
            $defaultValue = $parameter->getDefaultValue();
        } else {
            if ($recentValue) {
                $defaultString = ' [default: ' . $recentValue . ']';
                $defaultValue = $recentValue;
            } else {
                $defaultString = '';
                $defaultValue = '';
            }
        }

        return [
            'string' => $defaultString,
            'value' => $defaultValue
        ];
    }

    /**
     * Ask the user if the run should be allowed although the signature of the command has changed.
     */
    private function handleChecksum(Command $command, string $repositoryIdentifier, QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output): bool
    {
        $configHandler = $this->getConfigHandler();
        $hasChanged = $configHandler->hasChecksumChanged($command, $repositoryIdentifier);

        if ($hasChanged) {
            return $questionHelper->ask($input, $output, new ConfirmationQuestion('  The signature of the command has changed since you last run it. Do you confirm to still run it? [y/n] ', false));
        } else {
            return true;
        }
    }

    /**
     * Run every single command in the executable command.
     */
    private function executeCommand(OutputInterface $output, Prompt $prompt): void
    {
        $commands = CommandRunner::promptToMultilinePrompt($prompt);

        $commandRunner = new CommandRunner($this->getHistoryHandler());

        foreach ($commands as $commandPrompt) {
            $result = $commandRunner->execute($commandPrompt);
            $execOutput = $result->getOutput();

            if ($result->getResultCode() != SymfonyCommand::SUCCESS) {
                if (count($result->getOutput()) > 0) {
                    $this->renderErrorBox('Error executing prompt: ' . $execOutput[0]);
                } else {
                    $this->renderErrorBox('Error executing prompt.');
                }
            } else {
                if (count($execOutput) > 0) {
                    $this->renderInfoBox('Output: ');
                    $output->writeln($execOutput);
                } else {
                    $this->renderInfoBox('No output from command');
                }
            }
        }
    }
}
