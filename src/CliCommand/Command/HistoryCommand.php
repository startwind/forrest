<?php

namespace Startwind\Forrest\CliCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HistoryCommand extends CommandCommand
{
    protected static $defaultName = 'commands:history';
    protected static $defaultDescription = 'Show the latest commands that were executed.';

    protected function configure()
    {
        parent::configure();
        $this->setAliases(['history']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $historyHandler = $this->getHistoryHandler();

        $commands = $historyHandler->getEntries();

        $output->writeln('');

        $count = 1;

        foreach ($commands as $command) {
            $output->write($count . '  ' . $command);
            $count++;
        }

        return SymfonyCommand::SUCCESS;
    }
}
