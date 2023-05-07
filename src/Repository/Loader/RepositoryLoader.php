<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Repository\RepositoryCollection;

interface RepositoryLoader
{
    public function getIdentifiers(): array;

    public function enrich(RepositoryCollection $repositoryCollection);
}
