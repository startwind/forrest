<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

interface SearchAware
{
    /**
     * Return all commands  that a build for a special filename or file type.
     *
     * @return Command[]
     */
    public function searchByFile(array $files): array;

    /**
     * Return all commands that include a given pattern within the name
     * or description.
     *
     * @return Command[]
     */
    public function searchByPattern(array $patterns): array;

    /**
     * Return all commands that use one of the given tools.
     *
     * @return Command[]
     */
    public function searchByTools(array $tools): array;
}
