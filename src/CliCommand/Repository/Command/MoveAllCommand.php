<?php

namespace Startwind\Forrest\CliCommand\Repository\Command;

use Startwind\Forrest\CliCommand\Repository\RepositoryCommand;
use Startwind\Forrest\Repository\EditableRepository;
use Startwind\Forrest\Repository\ListAware;
use Startwind\Forrest\Repository\RepositoryCollection;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class MoveAllCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:command:move:all';
    protected static $defaultDescription = 'Move all commands from one to another repository.';

    protected function configure()
    {
        parent::configure();
        $this->addArgument('sourceRepository', InputArgument::REQUIRED, 'The identifier of the repository you want to move the command.');
        $this->addArgument('destinationRepository', InputArgument::REQUIRED, 'The identifier of the repository you want to move the command.');
        $this->addOption('removeAfterMove', 'r', InputOption::VALUE_NONE, 'The identifier of the repository you want to move the command.');
        $this->addOption('prefix', 'p', InputOption::VALUE_OPTIONAL, 'The identifier of the repository you want to move the command.', '');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->enrichRepositories();

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $destinationRepositoryName = $input->getArgument('destinationRepository');
        $sourceRepositoryName = $input->getArgument('sourceRepository');

        $sourceRepository = $this->getRepositoryCollection()->getRepository($sourceRepositoryName);
        $destinationRepository = $this->getRepositoryCollection()->getRepository($destinationRepositoryName);

        if (!$sourceRepository instanceof ListAware) {
            throw new \RuntimeException('The given repository "' . $destinationRepositoryName . '" is not listable.');
        }

        if (!$destinationRepository instanceof EditableRepository) {
            throw new \RuntimeException('The given repository "' . $destinationRepositoryName . '" is read-only.');
        }

        $commands = $sourceRepository->getCommands();

        if (count($commands) == 0) {
            OutputHelper::writeInfoBox($output, 'No commands found in "' . $sourceRepositoryName . '". Cancelling move command.');
            return Command::SUCCESS;
        }

        OutputHelper::writeInfoBox($output, 'We are moving the following commands to the "' . $destinationRepositoryName . '" repository.');

        \Startwind\Forrest\Output\OutputHelper::renderCommands($output, $input, $questionHelper, $commands);

        $output->writeln('');

        $move = $questionHelper->ask($input, $output, new ConfirmationQuestion('  Are you sure you want to move these command? (y/n) ', false));

        if (!$move) {
            return Command::FAILURE;
        }

        $prefix = $input->getOption('prefix');

        if ($prefix) $prefix = $prefix . RepositoryCollection::COMMAND_SEPARATOR;

        foreach ($commands as $command) {

            $command->setName($prefix . $command->getName());

            $destinationRepository->addCommand($command);

            if ($sourceRepository instanceof EditableRepository) {
                if ($input->getOption('removeAfterMove')) {
                    $sourceRepository->removeCommand($command->getName());
                }
            }
        }

        OutputHelper::writeInfoBox($output, 'Successfully moved ' . count($commands) . ' commands to "' . $destinationRepositoryName . '".');

        return Command::SUCCESS;

    }
}
