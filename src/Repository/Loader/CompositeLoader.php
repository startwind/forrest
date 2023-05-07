<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Repository\RepositoryCollection;

class CompositeLoader implements RepositoryLoader
{
    /**
     * @var RepositoryLoader[]
     */
    private array $loaders = [];

    public function addLoader(string $identifier, RepositoryLoader $loader): void
    {
        $this->loaders[$identifier] = $loader;
    }

    public function getIdentifiers(): array
    {
        $identifiers = [];

        foreach ($this->loaders as $loader) {
            $identifiers = array_merge($identifiers, $loader);
        }

        return $identifiers;
    }

    public function enrich(RepositoryCollection $repositoryCollection): void
    {
        foreach ($this->loaders as $loader) {
            $loader->enrich($repositoryCollection);
        }
    }

}
