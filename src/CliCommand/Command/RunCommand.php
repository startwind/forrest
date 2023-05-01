<?php

namespace Startwind\Forrest\CliCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class RunCommand extends CommandCommand
{
    protected static $defaultName = 'commands:run';
    protected static $defaultDescription = 'Run a specific command.';

    protected function configure()
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The commands identifier.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->enrichRepositories();
        $questionHelper = $this->getHelper('question');

        $command = $this->getCommand($input->getArgument('identifier'));

        $this->showCommandInformation($output, $command);

        $parameters = $command->getParameters();
        $values = [];

        foreach ($parameters as $parameter) {
            $values[$parameter] = $questionHelper->ask($input, $output, new Question('  Select value for ' . $parameter . ': '));
        }

        $prompt = $command->getPrompt($values);

        if (count($values) > 0) {
            $output->writeln('');
            $output->writeln('  Final prompt: ');
            $this->writeInfo($output, $prompt);
        }

        $doTheRun = $questionHelper->ask($input, $output, new ConfirmationQuestion('  Are you sure you want to run that command? [y/n] ', false));

        if ($doTheRun) {
            $output->writeln('');
            $this->executeCommand($output, $prompt);
        }

        return SymfonyCommand::SUCCESS;
    }

    /**
     * Run every single command in the executable command.
     */
    private function executeCommand(OutputInterface $output, string $prompt): void
    {
        $commands = explode("\n", $prompt);

        foreach ($commands as $command) {
            exec($command, $execOutput, $resultCode);
            if ($resultCode != SymfonyCommand::SUCCESS) {
                if (count($execOutput) > 0) {
                    $this->writeWarning($output, 'Error executing prompt: ' . $execOutput[0]);
                } else {
                    $this->writeWarning($output, 'Error executing prompt.');
                }
            } else {
                $this->writeInfo($output, $execOutput);
            }
        }
    }
}
