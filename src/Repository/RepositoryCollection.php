<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Logger\ForrestLogger;

class RepositoryCollection implements SearchAware
{
    public const COMMAND_SEPARATOR = ':';

    /**
     * @var Repository[]
     */
    private array $repositories = [];

    public function addRepository(string $identifier, Repository $repository): void
    {
        $this->repositories[$identifier] = $repository;
    }

    /**
     * Return all repositories
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }

    /**
     * Get a repository by identifier.
     */
    public function getRepository($identifier): Repository
    {
        if (!array_key_exists($identifier, $this->repositories)) {
            throw new \RuntimeException('No repository with identifier "' . $identifier . '" found.');
        }

        return $this->repositories[$identifier];
    }

    /**
     * @inheritDoc
     */
    public function searchByFile(array $files): array
    {
        $fileCommands = [];

        foreach ($this->repositories as $repositoryIdentifier => $repository) {
            if ($repository instanceof SearchAware) {
                try {
                    $foundCommands = $repository->searchByFile($files);
                } catch (\Exception $exception) {
                    ForrestLogger::error("Unable to \"search by file\" in repository " . $repositoryIdentifier . ' (' . $exception->getMessage() . ')');
                    continue;
                }
                foreach ($foundCommands as $foundCommand) {
                    $fileCommands[self::createUniqueCommandName($repositoryIdentifier, $foundCommand)] = $foundCommand;
                }
            }
        }

        return $fileCommands;
    }

    /**
     * @inheritDoc
     */
    public function searchByPattern(array $patterns): array
    {
        $commands = [];

        foreach ($this->repositories as $repositoryIdentifier => $repository) {
            if ($repository instanceof SearchAware) {
                $foundCommands = $repository->searchByPattern($patterns);
                foreach ($foundCommands as $foundCommand) {
                    $commands[RepositoryCollection::createUniqueCommandName($repositoryIdentifier, $foundCommand)] = $foundCommand;
                }
            }
        }

        return $commands;
    }

    /**
     * @inheritDoc
     */
    public function searchByTools(array $tools): array
    {
        $commands = [];

        foreach ($this->repositories as $repositoryIdentifier => $repository) {
            if ($repository instanceof SearchAware) {
                $foundCommands = $repository->searchByTools($tools);
                foreach ($foundCommands as $foundCommand) {
                    $commands[RepositoryCollection::createUniqueCommandName($repositoryIdentifier, $foundCommand)] = $foundCommand;
                }
            }
        }

        return $commands;
    }

    /**
     * @return \Startwind\Forrest\Command\Tool\Tool[]
     */
    public function getToolInformation(string $tool): array
    {
        $toolInformation = [];

        foreach ($this->getRepositories() as $repoName => $repository) {
            if ($repository instanceof ToolAware) {
                $toolInfo = $repository->findToolInformation($tool);
                if ($toolInfo) {
                    $toolInformation[$repoName] = $repository->findToolInformation($tool);
                }
            }
        }

        return $toolInformation;
    }

    /**
     * Return a command by the fully qualified command name.
     */
    public function getCommand(string $fullyQualifiedCommandName): Command
    {
        $repositoryIdentifier = RepositoryCollection::getRepositoryIdentifier($fullyQualifiedCommandName);
        $commandName = RepositoryCollection::getCommandName($fullyQualifiedCommandName);

        try {
            $command = $this->getRepository($repositoryIdentifier)->getCommand($commandName);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Unable to load command from ' . $repositoryIdentifier . ': ' . lcfirst($exception->getMessage()));
        }

        $command->setFullyQualifiedIdentifier($fullyQualifiedCommandName);

        return $command;
    }

    /**
     * Return the identifier of the repository from a full command name.
     */
    public static function getRepositoryIdentifier(string $identifier): string
    {
        return substr($identifier, 0, strpos($identifier, self::COMMAND_SEPARATOR));
    }

    public static function getCommandName(string $fullyQualifiedCommandName): string
    {
        return substr($fullyQualifiedCommandName, strpos($fullyQualifiedCommandName, self::COMMAND_SEPARATOR) + 1);
    }

    public static function createUniqueCommandName(string $repositoryIdentifier, Command $command): string
    {
        return $repositoryIdentifier . ':' . $command->getName();
    }

    public function pushStatus(string $fullyQualifiedCommandName, string $status): void
    {
        $repositoryIdentifier = RepositoryCollection::getRepositoryIdentifier($fullyQualifiedCommandName);
        $commandName = RepositoryCollection::getCommandName($fullyQualifiedCommandName);

        $repository = $this->getRepository($repositoryIdentifier);

        if ($repository instanceof StatusAwareRepository) {
            try {
                $repository->pushStatus($commandName, $status);
            } catch (\Exception $exception) {
                ForrestLogger::warn($exception->getMessage());
            }
        }
    }
}
