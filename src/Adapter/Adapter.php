<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;

interface Adapter
{
    /**
     * Return the type of the adapter
     */
    public function getType(): string;

    /**
     * Return all commands behind this repository.
     *
     * @return Command[]
     */
    public function getCommands(): array;

    /**
     * Return an initialized adapter via config array.
     */
    public static function fromConfigArray(array $config, Client $client): Adapter;

    /**
     * Return true if the repository can be edited.
     */
    public function isEditable(): bool;

    /**
     * Add a new command to the repository and persist it already.
     */
    public function addCommand(Command $command): void;

    /**
     * Remove command from the repository and persist it already.
     */
    public function removeCommand(string $commandName): void;
}
