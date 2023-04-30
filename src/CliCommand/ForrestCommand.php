<?php

namespace Startwind\Forrest\CliCommand;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Repository\Loader\YamlLoader;
use Startwind\Forrest\Repository\RepositoryCollection;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class ForrestCommand extends SymfonyCommand
{
    const COMMAND_SEPARATOR = ':';

    const DEFAULT_CONFIG_FILE = __DIR__ . '/../../config/default.yml';

    private RepositoryCollection $repositoryCollection;

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

    protected function initRepositories(): void
    {
        $client = new Client();

        $yamlLoader = new YamlLoader(self::DEFAULT_CONFIG_FILE, $client);

        $this->repositoryCollection = new RepositoryCollection();
        $yamlLoader->enrich($this->repositoryCollection);
    }

    protected function getCommand(string $identifier): Command
    {
        $repositoryIdentifier = substr($identifier, 0, strpos($identifier, self::COMMAND_SEPARATOR));
        $commandName = substr($identifier, strpos($identifier, self::COMMAND_SEPARATOR) + 1);

        $this->initRepositories();

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
