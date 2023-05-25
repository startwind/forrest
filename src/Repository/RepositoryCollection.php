<?php

namespace Startwind\Forrest\Repository;

class RepositoryCollection
{
    /**
     * @var FileRepository[]
     */
    private array $repositories = [];

    public function addRepository(string $identifier, FileRepository $repository): void
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
    public function getRepository($identifier): FileRepository
    {
        if (!array_key_exists($identifier, $this->repositories)) {
            throw new \RuntimeException('No repository with identifier "' . $identifier . '" found.');
        }

        return $this->repositories[$identifier];
    }
}
