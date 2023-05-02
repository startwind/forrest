<?php

namespace Startwind\Forrest\CliCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HistoryCommand extends CommandCommand
{
    protected static $defaultName = 'commands:history';
    protected static $defaultDescription = 'List all command that are registered in the repositories.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $historyHandler = $this->getHistoryHandler();

        $commands = $historyHandler->getEntries();

        $output->writeln('');

        foreach ($commands as $command) {
            $output->writeln($command);
        }

        return SymfonyCommand::SUCCESS;
    }
}
