<?php

namespace Startwind\Forrest\CliCommand;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\History\HistoryHandler;
use Startwind\Forrest\Repository\Loader\YamlLoader;
use Startwind\Forrest\Repository\RepositoryCollection;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ForrestCommand extends SymfonyCommand
{
    const COMMAND_SEPARATOR = ':';

    const DEFAULT_CONFIG_FILE = __DIR__ . '/../../config/default.yml';
    const USER_CONFIG_DIR = '.forrest';
    const USER_CONFIG_FILE = self::USER_CONFIG_DIR . '/config.yml';
    const USER_HISTORY_FILE = self::USER_CONFIG_DIR . '/history';

    private RepositoryCollection $repositoryCollection;

    private ?YamlLoader $yamlLoader = null;

    /**
     * Render a table with the statistics.
     */
    protected function renderTable(OutputInterface $output, array $headers, array $rows): void
    {
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
    }

    protected function getYamlLoader(): YamlLoader
    {
        return $this->yamlLoader;
    }

    protected function getUserConfigFile(): string
    {
        $home = getenv("HOME");
        return $home . DIRECTORY_SEPARATOR . self::USER_CONFIG_FILE;
    }

    protected function getHistoryHandler(): HistoryHandler
    {
        $home = getenv("HOME");
        return new HistoryHandler($home . DIRECTORY_SEPARATOR . self::USER_HISTORY_FILE);
    }

    private function createUserConfig()
    {
        $userConfigFile = $this->getUserConfigFile();

        if (!file_exists($userConfigFile)) {
            $dir = dirname($userConfigFile);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($userConfigFile, file_get_contents(self::DEFAULT_CONFIG_FILE));
        }
    }

    protected function initYamlLoader(): void
    {
        $this->createUserConfig();

        if (!$this->yamlLoader) {
            $client = new Client();
            $this->yamlLoader = new YamlLoader($this->getUserConfigFile(), self::DEFAULT_CONFIG_FILE, $client);
        }
    }

    protected function enrichRepositories(): void
    {
        $this->initYamlLoader();

        $this->repositoryCollection = new RepositoryCollection();
        $this->yamlLoader->enrich($this->repositoryCollection);
    }

    protected function getCommand(string $identifier): Command
    {
        $repositoryIdentifier = substr($identifier, 0, strpos($identifier, self::COMMAND_SEPARATOR));
        $commandName = substr($identifier, strpos($identifier, self::COMMAND_SEPARATOR) + 1);

        $this->enrichRepositories();

        foreach ($this->getRepositoryCollection()->getRepositories() as $key => $repository) {
            if ($key === $repositoryIdentifier) {
                foreach ($repository->getCommands() as $command) {
                    if ($command->getName() == $commandName) {
                        return $command;
                    }
                }
            }
        }

        throw new \RuntimeException('No command found with name ' . $identifier . '.');
    }

    protected function writeWarning(OutputInterface $output, string|array $message): void
    {
        OutputHelper::writeErrorBox($output, $message);
    }

    protected function writeInfo(OutputInterface $output, string|array $message): void
    {
        OutputHelper::writeInfoBox($output, $message);
    }

    protected function getRepositoryCollection(): RepositoryCollection
    {
        return $this->repositoryCollection;
    }
}
