<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

/**
 * ListAware repositories can be shown when the commands:list command is called. For
 * example the most private repos and YAML repos are list-aware, the database-based only
 * normally are not because they contain to many commands.
 */
interface ListAware
{
    /**
     * Return all commands in the repository.
     *
     * @return Command[]
     */
    public function getCommands(): array;

    /**
     * Return true if the repository has commands.
     */
    public function hasCommands(): bool;
}
