<?php

namespace Startwind\Forrest\CliCommand\Repository;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:remove';
    protected static $defaultDescription = 'Remove a specific repository.';

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The repositories identifier.');
    }

    protected function isInstalled(string $identifier): bool
    {
        $installedIdentifiers = $this->getRepositoryLoader()->getIdentifiers();
        return in_array($identifier, $installedIdentifiers);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->initRepositoryLoader();

        $identifier = $input->getArgument('identifier');

        if (!$this->isInstalled($identifier)) {
            $this->renderErrorBox('The given repository "' . $identifier . '" is not installed.');
            return SymfonyCommand::FAILURE;
        }

        $userConfigFile = $this->getUserConfigFile();

        if (!file_exists($userConfigFile)) {
            return SymfonyCommand::SUCCESS;
        }

        $configHandler = $this->getConfigHandler();
        $config = $configHandler->parseConfig();
        $config->removeRepository($identifier);
        $configHandler->dumpConfig($config);

        $this->renderInfoBox('Successfully removed repository with identifier "' . $identifier . '".');

        return SymfonyCommand::SUCCESS;
    }
}
