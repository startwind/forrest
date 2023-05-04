<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Adapter\Adapter;
use Startwind\Forrest\Command\Command;

class Repository
{
    public function __construct(
        private readonly Adapter $adapter,
        private readonly string $name,
        private readonly string $description
    ) {
    }

    public function getAdapter(): Adapter
    {
        return $this->adapter;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Command[]
     */
    public function getCommands(): array
    {
        return $this->adapter->getCommands();
    }

    /**
     * Return true if the repository can be edited
     */
    public function isEditable(): bool
    {
        return $this->getAdapter()->isEditable();
    }

    public function addCommand(Command $command): void
    {
        $this->adapter->addCommand($command);
    }
}
