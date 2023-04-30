<?php

namespace Startwind\Forrest\Adapter;

interface Adapter
{
    public function getType(): string;

    /**
     * Return all commands behind this repository.
     *
     * @return \Startwind\Forrest\Command\Command[]
     */
    public function getCommands(): array;
}
