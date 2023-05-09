<?php

namespace Startwind\Forrest\Repository;

class RepositoryCollection
{
    /**
     * @var Repository[]
     */
    private array $repositories = [];

    public function addRepository(string $identifier, Repository $repository): void
    {
        $this->repositories[$identifier] = $repository;
    }

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
}
