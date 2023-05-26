<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

class RepositoryCollection implements SearchAware
{
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
                $foundCommands = $repository->searchByFile($files);
                foreach ($foundCommands as $foundCommand) {
                    $fileCommands[RepositoryCollection::createUniqueCommandName($repositoryIdentifier, $foundCommand)] = $foundCommand;
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

    public static function createUniqueCommandName(string $repositoryIdentifier, Command $command): string
    {
        return $repositoryIdentifier . ':' . $command->getName();
    }
}
