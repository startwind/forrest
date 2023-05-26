<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\Output\PromptHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Startwind\Forrest\Repository\RepositoryCollection;

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

        $commandIdentifier = $input->getArgument('identifier');
        $repositoryIdentifier = RepositoryCollection::getRepositoryIdentifier($commandIdentifier);

        /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $promptHelper = new PromptHelper($this->getInput(), $this->getOutput(), $questionHelper, $this->getRecentParameterMemory());

        $command = $this->getCommand($commandIdentifier);

        $prompt = $promptHelper->askForPrompt($command, []);

        $promptHelper->showFinalPrompt($prompt);

        return SymfonyCommand::SUCCESS;
    }
}
