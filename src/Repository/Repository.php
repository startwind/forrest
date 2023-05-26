<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

interface Repository
{
    public const TYPE_FILE = 'file';
    public const TYPE_API = 'api';

    /**
     * Return the name of the repository.
     */
    public function getName(): string;

    /**
     * Return the description of the repository.
     */
    public function getDescription(): string;

    /**
     * Return a single command from a repository by identifier.
     */
    public function getCommand(string $identifier): Command;

    /**
     * Special directories are highlighted when the commands are listed.
     * Examples are context-sensitive repos like composer.json files.
     */
    public function isSpecial(): bool;

    /**
     * Assert that this repository is healthy and can be read.
     *
     * @throws \Exception
     */
    public function assertHealth(): void;
}
