<?php

namespace Startwind\Forrest\CliCommand\Repository\Command;

use Startwind\Forrest\CliCommand\Repository\RepositoryCommand;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Prompt;
use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AddCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:command:add';
    protected static $defaultDescription = 'Creates a boilerplate for a new command repository.';

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->renderInfoBox([
            'Create a new command. If you want to create more complex commands',
            'please use a text editor/IDE and edit the YAML file manually.',
            '',
            'Please select a repository you want to add a command to. Only ',
            'editable repositories are shown.'
        ]);

        $this->enrichRepositories();

        $repositories = $this->getRepositoryCollection()->getRepositories();

        $rows = [];
        $editableRepositories = [];
        $count = 0;

        foreach ($repositories as $repository) {
            if ($repository->isEditable()) {
                $count++;
                $editableRepositories[$count] = $repository;
                $rows[] = [
                    $count,
                    $repository->getName(),
                    $repository->getDescription()
                ];
            }
        }

        if ($count == 0) {
            $this->renderErrorBox('No editable repository found. Please create one using the repository:create command.');
            return SymfonyCommand::FAILURE;
        }

        OutputHelper::renderTable($output, ['ID', 'Name', 'Description'], $rows);

        $output->writeln('');

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $repoId = $this->chooseRepository($output, $input, $questionHelper, $count);
        $repo = $editableRepositories[$repoId];

        $output->writeln('');

        $commandName = $questionHelper->ask($input, $output, new Question('Command name [example: "files:find"]: '));
        $commandDescription = $questionHelper->ask($input, $output, new Question('Command description: '));
        $commandPrompt = $questionHelper->ask($input, $output, new Question('Command prompt: '));

        $repo->addCommand(new Command($commandName, $commandDescription, new Prompt($commandPrompt)));

        $this->renderInfoBox('Successfully added a new command.');

        return SymfonyCommand::SUCCESS;
    }

    /**
     * Choose a repository that is editable.
     */
    private function chooseRepository(OutputInterface $output, InputInterface $input, QuestionHelper $questionHelper, int $count): int
    {
        $repoId = 0;

        while ($repoId == 0) {
            $repoId = $questionHelper->ask($input, $output, new Question('Which repository do you want to edit [1-' . $count . ']? '));
            if ((int)$repoId < 0 || $repoId > $count) {
                $output->writeln('The ID must be between 1 and ' . $count . '. Please chose again: ');
                $repoId = 0;
            }
        }

        return (int)$repoId;
    }
}
