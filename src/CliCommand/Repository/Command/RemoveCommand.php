<?php

namespace Startwind\Forrest\CliCommand\Repository\Command;

use Startwind\Forrest\CliCommand\Repository\RepositoryCommand;
use Startwind\Forrest\Repository\EditableRepository;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RemoveCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:command:remove';
    protected static $defaultDescription = 'Creates a boilerplate for a new command repository.';

    protected function configure()
    {
        $this->addArgument('commandName', InputArgument::REQUIRED, 'The name of the command you want to remove');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->enrichRepositories();

        $identifier = $input->getArgument('commandName');

        $repositoryIdentifier = $this->getRepositoryIdentifier($identifier);

        $repository = $this->getRepositoryCollection()->getRepository($repositoryIdentifier);

        if (!$repository instanceof EditableRepository) {
            throw new \RuntimeException('The given repository "' . $repositoryIdentifier . '" is read-only.');
        }

        OutputHelper::writeWarningBox($output, 'Removing ' . $identifier . '. Please notice that this removing can not be undone.');

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $remove = $questionHelper->ask($input, $output, new ConfirmationQuestion('  Are you sure you want to remove the command? (y/n) ', false));

        if (!$remove) {
            return Command::FAILURE;
        }

        $commandName = $this->getCommandName($identifier);

        try {
            $repository->removeCommand($commandName);
        } catch (\Exception $exception) {
            OutputHelper::writeErrorBox($output, 'Unable to remove command from "' . $repositoryIdentifier . '". ' . $exception->getMessage());
            return Command::FAILURE;
        }

        OutputHelper::writeInfoBox($output, 'Successfully removed "' . $identifier . '" from "' . $repositoryIdentifier . '".');

        return Command::SUCCESS;
    }
}
