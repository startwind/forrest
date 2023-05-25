<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\FileRepository;
use Startwind\Forrest\Repository\SearchAwareRepository;
use Startwind\Forrest\Runner\CommandRunner;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ToolCommand extends SearchCommand
{
    protected static $defaultName = 'search:tool';
    protected static $defaultDescription = 'Search for commands that fit the given tool.';

    protected function configure(): void
    {
        $this->addArgument('tool', InputArgument::REQUIRED, 'The tool name you want to search for.');
        $this->setAliases(['tool']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $tool = $input->getArgument('tool');

        $this->renderInfoBox('This is a list of commands that match the given tool.');

        $commands = [];

        foreach ($this->getRepositoryCollection()->getRepositories() as $repositoryIdentifier => $repository) {
            if ($repository instanceof SearchAwareRepository) {
                $foundCommands = $repository->searchByTools([$tool]);
                foreach ($foundCommands as $foundCommand) {
                    $commands[FileRepository::createUniqueCommandName($repositoryIdentifier, $foundCommand)] = $foundCommand;
                }
            }
        }

        if (!empty($commands)) {
            /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');
            OutputHelper::renderCommands($output, $input, $questionHelper, $commands);
        } else {
            $this->renderErrorBox('No commands found that match this tool.');
        }

        $output->writeln('');

        return SymfonyCommand::SUCCESS;
    }
}
