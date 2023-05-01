<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends DirectoryCommand
{

    protected static $defaultName = 'directory:install';
    protected static $defaultDescription = 'List all repositories in the official Forrest directory.';

    protected function configure()
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The repositories identifier');
    }

    protected function isInstalled(string $identifier)
    {
        $installedIdentifiers = $this->getYamlLoader()->getIdentifiers();
        return in_array($identifier, $installedIdentifiers);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initYamlLoader();

        $directory = $this->getDirectory();
        $identifier = $input->getArgument('identifier');
        $repositories = $directory['repositories'];

        if (!array_key_exists($identifier, $repositories)) {
            $this->writeWarning($output, 'No repository with identifier "' . $identifier . '" found.');
            return SymfonyCommand::FAILURE;
        }

        $repoToInstall = $repositories[$identifier];

        if ($this->isInstalled($identifier)) {
            $this->writeWarning($output, 'The given repository "' . $identifier . '" is already installed.');
            return SymfonyCommand::FAILURE;
        }



        var_dump($repoToInstall);

        return SymfonyCommand::SUCCESS;
    }
}
