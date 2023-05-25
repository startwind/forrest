<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Adapter\Adapter;
use Startwind\Forrest\Command\Command;

interface Repository
{
    public function getAdapter(): Adapter;

    public function getName(): string;

    public function getDescription(): string;

    /**
     * @return Command[]
     */
    public function getCommands(): array;

    public function hasCommands(): bool;

    /**
     * Special directories are highlighted when the commands are listed.
     * Examples are context-sensitive repos like composer.json files.
     */
    public function isSpecial(): bool;

    public static function createUniqueCommandName(string $repositoryIdentifier, Command $command): string;
}
