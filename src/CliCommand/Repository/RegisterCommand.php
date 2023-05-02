<?php

namespace Startwind\Forrest\CliCommand\Repository;

use Startwind\Forrest\Config\ConfigFileHandler;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class RegisterCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:register';
    protected static $defaultDescription = 'Add an existing local repository.';

    protected function configure()
    {
        $this->addArgument('repositoryFileName', InputArgument::REQUIRED, 'The filename of the repository.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repositoryFileName = $input->getArgument('repositoryFileName');

        if (!file_exists($repositoryFileName)) {
            $this->writeWarning($output, 'File "' . $repositoryFileName . '" not found.');
            return SymfonyCommand::FAILURE;
        }

        $newRepoContent = Yaml::parse(file_get_contents($repositoryFileName));

        $defaultName = 'Local Repository';
        $defaultDescription = 'This repository contains all commands needed for local stuff.';
        $defaultIdentifier = '';

        if (array_key_exists('repository', $newRepoContent)) {
            if (array_key_exists('name', $newRepoContent['repository'])) {
                $defaultName = $newRepoContent['repository']['name'];
            }
            if (array_key_exists('description', $newRepoContent['repository'])) {
                $defaultDescription = $newRepoContent['repository']['description'];
            }
            if (array_key_exists('identifier', $newRepoContent['repository'])) {
                $defaultIdentifier = $newRepoContent['repository']['identifier'];
            }
        }

        $questionHelper = $this->getHelper('question');

        $name = $questionHelper->ask($input, $output, new Question('Name of the repository [default: ' . $defaultName . ']: ', $defaultName));

        if ($name != $defaultName) {
            $defaultIdentifier = $this->getIdentifierSuggestion($name);
        }

        $description = $questionHelper->ask($input, $output, new Question('Description of the repository: [default: ' . $defaultDescription . ']: ', $defaultDescription));
        $identifier = $questionHelper->ask($input, $output, new Question('Identifier of the repository [default: ' . $defaultIdentifier . ']: ', $defaultIdentifier));

        $this->registerRepository($identifier, $name, $description, $repositoryFileName);
        $this->writeInfo($output, 'Repository file "' . $repositoryFileName . '" successfully registered.');

        return SymfonyCommand::SUCCESS;
    }
}
