<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Startwind\Forrest\Config\ConfigFileHandler;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class InstallCommand extends DirectoryCommand
{
    protected static $defaultName = 'directory:install';
    protected static $defaultDescription = 'Install a specific repository from the official Forrest directory.';

    protected function configure(): void
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The repositories identifier');
    }

    protected function isInstalled(string $identifier): bool
    {
        $installedIdentifiers = $this->getRepositoryLoader()->getIdentifiers();
        return in_array($identifier, $installedIdentifiers);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->initRepositoryLoader();

        $directory = $this->getDirectory();
        $identifier = $input->getArgument('identifier');
        $repositories = $directory['repositories'];

        if (!array_key_exists($identifier, $repositories)) {
            $this->renderErrorBox('No repository with identifier "' . $identifier . '" found.');
            return SymfonyCommand::FAILURE;
        }

        $repoToInstall = $repositories[$identifier];

        if ($this->isInstalled($identifier)) {
            $this->renderErrorBox('The given repository "' . $identifier . '" is already installed.');
            return SymfonyCommand::FAILURE;
        }

        $userConfigFile = $this->getUserConfigFile();

        if (!file_exists($userConfigFile)) {
            $this->renderErrorBox('Unable to create config file "' . $userConfigFile . '". This is needed for adding a new repository.');
            return SymfonyCommand::FAILURE;
        }

        $configHandler = $this->getConfigHandler();

        $config = $configHandler->parseConfig();
        $config->addRepository($identifier, $repoToInstall);
        $configHandler->dumpConfig($config);

        $this->renderInfoBox('Successfully installed new repository. Use commands:list to see new commands.');

        return SymfonyCommand::SUCCESS;
    }
}
