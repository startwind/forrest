<?php

namespace Startwind\Forrest\CliCommand\Directory\Exception;

class DirectoriesLoadException extends MultiException
{
    private array $directories = [];

    public function getDirectories(): array
    {
        return $this->directories;
    }

    public function setDirectories(array $directories): void
    {
        $this->directories = $directories;
    }
}
