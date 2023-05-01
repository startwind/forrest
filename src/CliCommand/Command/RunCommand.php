<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\Command\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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

        $command = $this->getCommand($input->getArgument('identifier'));

        $this->showCommandInformation($output, $command);

        $doTheRun = $this->getHelper('question')->ask($input, $output, new ConfirmationQuestion('  Are you sure you want to run that command? [y/n] ', false));

        if ($doTheRun) {
            $output->writeln('');
            $this->executeCommand($command);
        }

        return SymfonyCommand::SUCCESS;
    }

    /**
     * Run every single command in the executable command.
     */
    private function executeCommand(Command $command): void
    {
        $commands = explode("\n", $command->getCommand());

        foreach ($commands as $command) {
            exec($command);
        }
    }
}
