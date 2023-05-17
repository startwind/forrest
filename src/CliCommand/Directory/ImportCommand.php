<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends DirectoryCommand
{
    public const COMMAND_NAME = 'directory:add';

    protected static $defaultName = self::COMMAND_NAME;
    protected static $defaultDescription = 'Add an external directory to the list.';

    protected function configure()
    {
        $this->addArgument('directoryConfig', InputArgument::REQUIRED, 'The config string for the directory.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        var_dump();
        return SymfonyCommand::SUCCESS;
    }
}
