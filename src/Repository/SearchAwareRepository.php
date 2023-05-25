<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

interface SearchAwareRepository
{
    /**
     * @return Command[]
     */
    public function searchByFile(array $files): array;

    /**
     * @return Command[]
     */
    public function searchByPattern(array $patterns): array;

    /**
     * @return Command[]
     */
    public function searchByTools(array $tools): array;
}
