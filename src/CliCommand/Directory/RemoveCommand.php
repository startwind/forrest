<?php

namespace Startwind\Forrest\CliCommand\Directory;

use Startwind\Forrest\Config\ConfigFileHandler;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class RemoveCommand extends DirectoryCommand
{
    protected static $defaultName = 'directory:remove';
    protected static $defaultDescription = 'Remove a specific repository.';

    protected function configure()
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The repositories identifier.');
    }

    protected function isInstalled(string $identifier): bool
    {
        $installedIdentifiers = $this->getYamlLoader()->getIdentifiers();
        return in_array($identifier, $installedIdentifiers);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initYamlLoader();

        $identifier = $input->getArgument('identifier');

        if (!$this->isInstalled($identifier)) {
            $this->writeWarning($output, 'The given repository "' . $identifier . '" is not installed.');
            return SymfonyCommand::FAILURE;
        }

        $userConfigFile = $this->getUserConfigFile();

        if (!file_exists($userConfigFile)) {
            return SymfonyCommand::SUCCESS;
        }

        $configHandler = new ConfigFileHandler($userConfigFile);
        $config = $configHandler->parse();
        $config->removeRepository($identifier);
        $configHandler->dump($config);

        $this->writeInfo($output, 'Successfully removed repository with identifier "' . $identifier . '".');

        return SymfonyCommand::SUCCESS;
    }
}
