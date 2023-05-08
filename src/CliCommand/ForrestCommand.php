<?php

namespace Startwind\Forrest\CliCommand;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Config\ConfigFileHandler;
use Startwind\Forrest\Config\RecentParameterMemory;
use Startwind\Forrest\History\HistoryHandler;
use Startwind\Forrest\Repository\Loader\CompositeLoader;
use Startwind\Forrest\Repository\Loader\LocalComposerRepositoryLoader;
use Startwind\Forrest\Repository\Loader\LocalPackageRepositoryLoader;
use Startwind\Forrest\Repository\Loader\LocalRepositoryLoader;
use Startwind\Forrest\Repository\Loader\RepositoryLoader;
use Startwind\Forrest\Repository\Loader\YamlLoader;
use Startwind\Forrest\Repository\RepositoryCollection;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ForrestCommand extends SymfonyCommand
{
    public const COMMAND_SEPARATOR = ':';

    public const DEFAULT_CONFIG_FILE = __DIR__ . '/../../config/repository.yml';

    public const DEFAULT_LOCAL_CONFIG_FILE = '.forrest.yml';
    public const USER_CONFIG_DIR = '.forrest';
    public const USER_CONFIG_FILE = self::USER_CONFIG_DIR . '/config.yml';
    public const USER_CHECKSUM_FILE = self::USER_CONFIG_DIR . '/checksum.json';

    public const USER_RECENT_FILE = self::USER_CONFIG_DIR . '/recent.json';
    public const USER_HISTORY_FILE = self::USER_CONFIG_DIR . '/history';

    private RepositoryCollection $repositoryCollection;

    private ?RepositoryLoader $repositoryLoader = null;

    private InputInterface $input;
    private OutputInterface $output;
    private RecentParameterMemory $recentParameterMemory;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $home = getenv("HOME");

        $this->recentParameterMemory = new RecentParameterMemory($home . DIRECTORY_SEPARATOR . self::USER_RECENT_FILE);

        return $this->doExecute($input, $output);
    }

    protected function getRecentParameterMemory(): RecentParameterMemory
    {
        return $this->recentParameterMemory;
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        return SymfonyCommand::SUCCESS;
    }

    protected function getInput(): InputInterface
    {
        return $this->input;
    }

    protected function getOutput(): OutputInterface
    {
        return $this->output;
    }

    protected function getRepositoryLoader(): RepositoryLoader
    {
        return $this->repositoryLoader;
    }

    protected function getUserConfigFile(): string
    {
        $home = getenv("HOME");
        return $home . DIRECTORY_SEPARATOR . self::USER_CONFIG_FILE;
    }

    protected function getUserChecksumsFile(): string
    {
        $home = getenv("HOME");
        return $home . DIRECTORY_SEPARATOR . self::USER_CHECKSUM_FILE;
    }

    protected function getConfigHandler(): ConfigFileHandler
    {
        return new ConfigFileHandler($this->getUserConfigFile(), $this->getUserChecksumsFile());
    }

    protected function getHistoryHandler(): HistoryHandler
    {
        $home = getenv("HOME");
        return new HistoryHandler($home . DIRECTORY_SEPARATOR . self::USER_HISTORY_FILE);
    }

    private function createUserConfig(): void
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

    protected function initRepositoryLoader(): void
    {
        $this->createUserConfig();

        if (!$this->repositoryLoader) {
            $client = new Client();

            $repositoryLoader = new CompositeLoader();

            $repositoryLoader->addLoader('defaultConfig', new YamlLoader($this->getUserConfigFile(), self::DEFAULT_CONFIG_FILE, $client));

            if (file_exists(self::DEFAULT_LOCAL_CONFIG_FILE)) {
                $repositoryLoader->addLoader('localConfig', new LocalRepositoryLoader(self::DEFAULT_LOCAL_CONFIG_FILE));
            }

            if (LocalComposerRepositoryLoader::isApplicable()) {
                $repositoryLoader->addLoader('localComposer', new LocalComposerRepositoryLoader());
            }

            if (LocalPackageRepositoryLoader::isApplicable()) {
                $repositoryLoader->addLoader('localPackagist', new LocalPackageRepositoryLoader());
            }

            $this->repositoryLoader = $repositoryLoader;
        }
    }

    protected function enrichRepositories(): void
    {
        $this->initRepositoryLoader();
        $this->repositoryCollection = new RepositoryCollection();
        $this->repositoryLoader->enrich($this->repositoryCollection);
    }

    /**
     * Return the command from the fully qualified command identifier.
     */
    protected function getCommand(string $identifier): Command
    {
        $repositoryIdentifier = $this->getRepositoryIdentifier($identifier);
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

    /**
     * Return the identifier of the repository from a full command name.
     */
    protected function getRepositoryIdentifier(string $identifier): string
    {
        $repositoryIdentifier = substr($identifier, 0, strpos($identifier, self::COMMAND_SEPARATOR));
        return $repositoryIdentifier;
    }

    /**
     * Write an error message in a beautiful box
     */
    protected function renderErrorBox(string|array $message): void
    {
        OutputHelper::writeErrorBox($this->getOutput(), $message);
    }

    /**
     * Write an info message in a beautiful box
     */
    protected function renderInfoBox(string|array $message): void
    {
        OutputHelper::writeInfoBox($this->getOutput(), $message);
    }

    /**
     * Write a warning message in a beautiful box
     */
    protected function renderWarningBox(string|array $message): void
    {
        OutputHelper::writeWarningBox($this->getOutput(), $message);
    }

    protected function renderInfo(string|array $message): void
    {
        $output = $this->getOutput();
        $output->writeln('');
        $output->writeln('<fg=yellow>' . $message . '</>');
        $output->writeln('');
    }

    protected function getRepositoryCollection(): RepositoryCollection
    {
        return $this->repositoryCollection;
    }
}
