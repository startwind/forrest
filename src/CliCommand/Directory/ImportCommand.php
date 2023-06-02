<?php

namespace Startwind\Forrest\CliCommand\Directory;

use GuzzleHttp\Client;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends DirectoryCommand
{
    public const COMMAND_NAME = 'directory:import';

    protected static $defaultName = self::COMMAND_NAME;
    protected static $defaultDescription = 'Add an external directory to the list.';

    protected function configure()
    {
        parent::configure();
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The config string for the directory.');
        $this->addArgument('directoryConfig', InputArgument::REQUIRED, 'The config string for the directory.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');
        $rawConfig = $input->getArgument('directoryConfig');
        $config = json_decode($rawConfig, true);

        if (!$config) {
            throw new \RuntimeException('The directory config seems to be broken.');
        }

        if (!is_array($config)) {
            throw new \RuntimeException('The directory config is not an array. Please check the json encoded string.');
        }

        $existingConfigs = $this->getDirectoryConfigs();

        if (array_key_exists($identifier, $existingConfigs)) {
            OutputHelper::writeErrorBox($output, [
                'A directory with the name ' . $identifier . ' already exists. ',
                'Please remove it before importing the new one.'
            ]);
            return SymfonyCommand::FAILURE;
        }

        try {
            $this->validateConfig($config);
        } catch (\Exception $exception) {
            OutputHelper::writeErrorBox($output, [
                'Ooops, something went wrong: ',
                $exception->getMessage()
            ]);
            return SymfonyCommand::FAILURE;
        }

        $existingConfigs[$identifier] = $config;

        $config = $this->getConfigHandler()->parseConfig();

        $config->setDirectories($existingConfigs);

        $this->getConfigHandler()->dumpConfig($config);

        OutputHelper::writeInfoBox($output, [
            'Successfully imported new directory.',
            'Use forrest directory:list ' . $identifier . ' to show the new repositories.'
        ]);

        return SymfonyCommand::SUCCESS;
    }

    protected function validateConfig(array $config)
    {
        $this->loadDirectory($config);
    }
}
