<?php

namespace Startwind\Forrest\CliCommand;

use GuzzleHttp\Client;
use Startwind\Forrest\Repository\Loader\YamlLoader;
use Startwind\Forrest\Repository\RepositoryCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class ForrestCommand extends Command
{
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

    protected function getRepositoryCollection(): RepositoryCollection
    {
        return $this->repositoryCollection;
    }
}
