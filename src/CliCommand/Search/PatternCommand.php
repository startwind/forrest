<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PatternCommand extends SearchCommand
{
    protected static $defaultName = 'search:pattern';
    protected static $defaultDescription = 'Search for commands that fit the given pattern.';

    protected function configure(): void
    {
        $this->addArgument('pattern', InputArgument::REQUIRED, 'The pattern you want to search for.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $pattern = $input->getArgument('pattern');

        $this->renderInfoBox('This is a list of commands that match the given pattern.');

        $commands = $this->search(function (Command $command, $config) {
            $pattern = $config['pattern'];

            if (str_contains($command->getName(), $pattern)) {
                return true;
            }

            if (str_contains($command->getDescription(), $pattern)) {
                return true;
            }

            return false;
        }, ['pattern' => $pattern]);

        if (!empty($commands)) {
            OutputHelper::renderCommands($output, $commands);
        } else {
            $this->renderErrorBox('No commands found that match this pattern.');
        }

        $output->writeln('');

        return SymfonyCommand::SUCCESS;
    }

}
