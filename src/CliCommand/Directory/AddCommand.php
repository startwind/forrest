<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends DirectoryCommand
{
    protected static $defaultName = 'directory:add';
    protected static $defaultDescription = 'Add an external directory to the list.';

    protected function configure()
    {
        $this->addArgument('directoryConfig', InputArgument::REQUIRED, 'The config string for the directory.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {

        return SymfonyCommand::SUCCESS;
    }
}
