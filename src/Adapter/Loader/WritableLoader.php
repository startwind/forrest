<?php

namespace Startwind\Forrest\Adapter\Loader;

interface WritableLoader
{
    /**
     * Add a command to the given file.
     */
    public function addCommand(string $commandName, array $command): void;

    /**
     * Remove a command from the given file
     */
    public function removeCommand(string $commandName): void;
}
