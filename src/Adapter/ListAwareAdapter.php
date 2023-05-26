<?php

namespace Startwind\Forrest\Adapter;

use Startwind\Forrest\Command\Command;

interface ListAwareAdapter extends Adapter
{
    /**
     * Return all commands from this repository.
     *
     * @return Command[]
     */
    public function getCommands(): array;
}
