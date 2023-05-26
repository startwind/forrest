<?php

namespace Startwind\Forrest\Repository;

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
     * Special directories are highlighted when the commands are listed.
     * Examples are context-sensitive repos like composer.json files.
     */
    public function isSpecial(): bool;
}
