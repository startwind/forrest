<?php

namespace Startwind\Forrest\Adapter;

use Startwind\Forrest\Command\Command;

interface EditableAdapter extends Adapter
{
    /**
     * Add a new command to the repository and persist it already.
     */
    public function addCommand(Command $command): void;

    /**
     * Remove command from the repository and persist it already.
     */
    public function removeCommand(string $commandName): void;

    /**
     * Return true if the current adapter is editable;
     */
    public function isEditable(): bool;
}
