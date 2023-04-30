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
}
