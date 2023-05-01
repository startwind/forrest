<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Startwind\Forrest\Repository\Loader\YamlLoader;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class InstallCommand extends DirectoryCommand
{
    protected static $defaultName = 'directory:install';
    protected static $defaultDescription = 'List all repositories in the official Forrest directory.';

    protected function configure()
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The repositories identifier');
    }

    protected function isInstalled(string $identifier): bool
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

        $userConfigFile = $this->getUserConfigFile();

        if (!file_exists($userConfigFile)) {
            $this->writeWarning($output, 'Unable to create config file "' . $userConfigFile . '". This is needed for adding a new repository.');
            return SymfonyCommand::FAILURE;
        }

        $config = Yaml::parse(file_get_contents($userConfigFile));

        $config['repositories'][$identifier] = $repoToInstall;

        $this->writeInfo($output, 'Successfully installed new repository. Use commands:list to see new commands.');

        file_put_contents($userConfigFile, Yaml::dump($config));

        return SymfonyCommand::SUCCESS;
    }
}
