<?php

namespace Startwind\Forrest\Adapter;

interface Adapter
{
    /**
     * Return the type of the adapter
     */
    public function getType(): string;

    /**
     * Return all commands behind this repository.
     *
     * @return \Startwind\Forrest\Command\Command[]
     */
    public function getCommands(): array;

    /**
     * Return a initialized adapter via config array.
     */
    static public function fromConfigArray(array $config): Adapter;
}
