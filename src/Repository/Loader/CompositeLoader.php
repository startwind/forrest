<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Repository\RepositoryCollection;

class CompositeLoader implements RepositoryLoader
{
    /**
     * @var RepositoryLoader[]
     */
    private array $loaders = [];

    /**
     * Add a loader to the component.
     */
    public function addLoader(string $identifier, RepositoryLoader $loader): void
    {
        $this->loaders[$identifier] = $loader;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifiers(): array
    {
        $identifiers = [];

        foreach ($this->loaders as $loader) {
            $identifiers = array_merge($identifiers, $loader->getIdentifiers());
        }

        return $identifiers;
    }

    /**
     * @inheritDoc
     */
    public function enrich(RepositoryCollection $repositoryCollection): void
    {
        foreach ($this->loaders as $loader) {
            $loader->enrich($repositoryCollection);
        }
    }
}
