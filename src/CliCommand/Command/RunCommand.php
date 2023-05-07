<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\Parameter;
use Startwind\Forrest\Runner\CommandRunner;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getArgument('identifier')) {
            $this->renderListCommand($input, $output);
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

        $values = $this->handleParameters($questionHelper, $input, $output, $parameters);

        $prompt = $command->getPrompt($values);

        if (count($values) > 0) {
            $output->writeln('');
            $output->writeln('  Final prompt: ');
            $this->renderInfoBox($output, CommandRunner::stringToMultilinePrompt($prompt));
        }

        if (!$command->isRunnable()) {
            $this->renderWarningBox($output, [
                'This command was marked as not callable from Forrest. Please copy the prompt and run it',
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

        $this->getConfigHandler()->persistChecksum($command, $repositoryIdentifier);

        $output->writeln('');
        $this->executeCommand($output, $prompt);

        return SymfonyCommand::SUCCESS;
    }

    /**
     * @param Parameter[] $parameters
     * @return array<string, mixed>
     */
    private function handleParameters(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, array $parameters): array
    {
        $values = [];

        foreach ($parameters as $identifier => $parameter) {

            if ($parameter->getName()) {
                $name = $identifier . ' (' . $parameter->getName() . ')';
            } else {
                $name = $identifier;
            }

            if ($parameter->getDefaultValue()) {
                $defaultString = ' [default: ' . $parameter->getDefaultValue() . ']';
                $defaultValue = $parameter->getDefaultValue();
            } else {
                $defaultString = '';
                $defaultValue = '';
            }

            if ($parameter->hasValues()) {
                $values[$identifier] = $questionHelper->ask($input, $output, new ChoiceQuestion('  Select value for ' . $name . $defaultString . ': ', $parameter->getValues(), $defaultValue));
            } else {
                $values[$identifier] = $questionHelper->ask($input, $output, new Question('  Select value for ' . $name . $defaultString . ': ', $defaultValue));
            }
        }

        return $values;
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
    private function executeCommand(OutputInterface $output, string $prompt): void
    {
        $commands = CommandRunner::stringToMultilinePrompt($prompt);

        foreach ($commands as $command) {
            $this->getHistoryHandler()->addEntry($command);
            exec($command, $execOutput, $resultCode);
            if ($resultCode != SymfonyCommand::SUCCESS) {
                if (count($execOutput) > 0) {
                    $this->renderErrorBox($output, 'Error executing prompt: ' . $execOutput[0]);
                } else {
                    $this->renderErrorBox($output, 'Error executing prompt.');
                }
            } else {
                if (count($execOutput) > 0) {
                    $this->renderInfoBox($output, 'Output: ');
                    $output->writeln($execOutput);
                } else {
                    $this->renderInfoBox($output, 'No output from command');
                }
            }
        }
    }
}
