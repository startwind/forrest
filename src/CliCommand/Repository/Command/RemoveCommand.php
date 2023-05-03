<?php

namespace Startwind\Forrest\CliCommand\Repository\Command;

use Startwind\Forrest\CliCommand\Repository\RepositoryCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class RemoveCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:command:remove';
    protected static $defaultDescription = 'Creates a boilerplate for a new command repository.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // show local repos
        // select local repo

        // add name
        // add description
        // add prompt

        return Command::SUCCESS;
    }
}
