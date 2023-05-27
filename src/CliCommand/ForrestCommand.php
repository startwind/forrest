<?php

namespace Startwind\Forrest\CliCommand;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Config\ConfigFileHandler;
use Startwind\Forrest\Config\RecentParameterMemory;
use Startwind\Forrest\History\HistoryHandler;
use Startwind\Forrest\Logger\ForrestLogger;
use Startwind\Forrest\Logger\OutputLogger;
use Startwind\Forrest\Repository\Loader\CompositeLoader;
use Startwind\Forrest\Repository\Loader\LocalComposerRepositoryLoader;
use Startwind\Forrest\Repository\Loader\LocalPackageRepositoryLoader;
use Startwind\Forrest\Repository\Loader\LocalRepositoryLoader;
use Startwind\Forrest\Repository\Loader\RepositoryLoader;
use Startwind\Forrest\Repository\Loader\YamlLoader;
use Startwind\Forrest\Repository\RepositoryCollection;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


abstract class ForrestCommand extends SymfonyCommand
{
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

    private Client $client;
    /**
     * @var true
     */
    private bool $enriched = false;

    protected function configure()
    {
        $this->addOption('debug', 'd', InputOption::VALUE_NONE, 'Show logs.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ForrestLogger::addLogger('output', new OutputLogger($output));

        if ($input->getOption('debug')) {
            ForrestLogger::setLogLevel(ForrestLogger::LEVEL_INFO);
        }

        $this->input = $input;
        $this->output = $output;


        $this->initClient();

        $home = getenv("HOME");

        $this->recentParameterMemory = new RecentParameterMemory($home . DIRECTORY_SEPARATOR . self::USER_RECENT_FILE);

        return $this->doExecute($input, $output);
    }

    private function initClient()
    {
        $this->client = new Client();
    }

    protected function getRecentParameterMemory(): RecentParameterMemory
    {
        return $this->recentParameterMemory;
    }

    /**
     * Do the actual execution.
     */
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

    private function getUserChecksumsFile(): string
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
            $repositoryLoader = new CompositeLoader();

            $repositoryLoader->addLoader('defaultConfig', new YamlLoader($this->getUserConfigFile(), self::DEFAULT_CONFIG_FILE, $this->getClient()));

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
        if (!$this->enriched) {
            $this->initRepositoryLoader();
            $this->repositoryCollection = new RepositoryCollection();
            $this->repositoryLoader->enrich($this->repositoryCollection);
            $this->enriched = true;
        }
    }

    /**
     * Return the command from the fully qualified command identifier.
     */
    protected function getCommand(string $fullyQualifiedCommandName): Command
    {
        $this->enrichRepositories();
        return $this->getRepositoryCollection()->getCommand($fullyQualifiedCommandName);
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

    protected function getRepositoryCollection(): RepositoryCollection
    {
        return $this->repositoryCollection;
    }

    protected function askQuestion($questionToAsk, bool $notEmpty = true): string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $answer = $questionHelper->ask($this->getInput(), $this->getOutput(), new Question($questionToAsk));

        if ($notEmpty && !$answer) {
            $this->getOutput()->writeln('The value must not be empty.');
            return $this->askQuestion($questionToAsk, $notEmpty);
        }

        return $answer;
    }

    /**
     * Return an initialized HTTP client. Be sure that every command uses this client
     * as caching and other things are already configured here.
     */
    protected function getClient(): Client
    {
        return $this->client;
    }
}
