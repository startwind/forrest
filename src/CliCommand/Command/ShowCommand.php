<?php

namespace Startwind\Forrest\CliCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends CommandCommand
{
    protected static $defaultName = 'commands:show';
    protected static $defaultDescription = 'Show a specific command. It will not run it.';

    protected function configure(): void
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The commands identifier.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->enrichRepositories();

        $command = $this->getCommand($input->getArgument('identifier'));

        $this->showCommandInformation($output, $command);

        return SymfonyCommand::SUCCESS;
    }
}
