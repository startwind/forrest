<?php

namespace Startwind\Forrest\CliCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends CommandCommand
{
    protected static $defaultName = 'commands:list';
    protected static $defaultDescription = 'List all command that are registered in the repositories.';

    protected function configure()
    {
        $this->addArgument('repository', InputArgument::OPTIONAL, 'Show only this single repository', '');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->renderListCommand($input->getArgument('repository'));
        return SymfonyCommand::SUCCESS;
    }
}
