<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\FileRepository;
use Startwind\Forrest\Repository\SearchAwareRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PatternCommand extends SearchCommand
{
    protected static $defaultName = 'search:pattern';
    protected static $defaultDescription = 'Search for commands that fit the given pattern.';

    protected function configure(): void
    {
        $this->addArgument('pattern', InputArgument::REQUIRED, 'The pattern you want to search for.');
        $this->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Run the command without asking for permission.', false);

        $this->setAliases(['pattern']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $pattern = $input->getArgument('pattern');

        $this->renderInfoBox('This is a list of commands that match the given pattern.');

        $commands = [];

        foreach ($this->getRepositoryCollection()->getRepositories() as $repositoryIdentifier => $repository) {
            if ($repository instanceof SearchAwareRepository) {
                $foundCommands = $repository->searchByPattern([$pattern]);
                foreach ($foundCommands as $foundCommand) {
                    $commands[FileRepository::createUniqueCommandName($repositoryIdentifier, $foundCommand)] = $foundCommand;
                }
            }
        }

        if (empty($commands)) {
            $this->renderErrorBox('No commands found that match the given pattern.');
        }

        return $this->runFromCommands($commands);
    }
}
