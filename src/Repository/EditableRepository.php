<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

interface EditableRepository
{
    /**
     * Add a command to the repository.
     */
    public function addCommand(Command $command): void;

    /**
     * Remove a command from the repository.
     */
    public function removeCommand(string $commandName): void;
}
