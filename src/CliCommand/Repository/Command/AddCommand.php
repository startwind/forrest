<?php

namespace Startwind\Forrest\CliCommand\Repository\Command;

use Startwind\Forrest\CliCommand\Repository\RepositoryCommand;
use Startwind\Forrest\Command\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AddCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:command:add';
    protected static $defaultDescription = 'Creates a boilerplate for a new command repository.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->writeInfo($output, [
            'Create a new command. If you want to create more complex commands',
            'please use a text editor/IDE and edit the YAML file manually.',
            '',
            'Please select a repository you want to add a command to. Only ',
            'editable repositories are shown.'
        ]);

        $this->enrichRepositories();

        $repositoryCollection = $this->getRepositoryCollection();

        $repositories = $repositoryCollection->getRepositories();

        $rows = [];
        $editableRepositories = [];
        $count = 0;

        foreach ($repositories as $key => $repository) {
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

        $this->renderTable($output, ['ID', 'Name', 'Description'], $rows);

        $output->writeln('');

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $repoId = $questionHelper->ask($input, $output, new Question('Which repository do you want to edit [1-' . $count . ']? '));

        $repo = $editableRepositories[$repoId];

        $output->writeln('');

        $commandName = $questionHelper->ask($input, $output, new Question('Command name [example: "files:find"]: '));
        $commandDescription = $questionHelper->ask($input, $output, new Question('Command description: '));
        $commandPrompt = $questionHelper->ask($input, $output, new Question('Command prompt: '));

        $repo->addCommand(new Command($commandName, $commandDescription, $commandPrompt));

        $this->writeInfo($output, 'Successfully added a new command.');

        return SymfonyCommand::SUCCESS;
    }
}
