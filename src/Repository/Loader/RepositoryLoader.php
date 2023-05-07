<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Repository\RepositoryCollection;

interface RepositoryLoader
{
    /**
     * Return all identifiers of repositories that were loaded via this loader.
     */
    public function getIdentifiers(): array;

    /**
     * Enrich the repository collection with all repositories that are attached to this loader.
     */
    public function enrich(RepositoryCollection $repositoryCollection);
}
