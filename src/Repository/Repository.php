<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Adapter\Adapter;
use Startwind\Forrest\Command\Command;

class Repository
{
    private Adapter $adapter;

    private string $name;

    private string $description;

    /**
     * The constructor
     */
    public function __construct(Adapter $adapter, string $name, string $description)
    {
        $this->adapter = $adapter;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return \Startwind\Forrest\Adapter\Adapter
     */
    public function getAdapter(): Adapter
    {
        return $this->adapter;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return \Startwind\Forrest\Command\Command[]
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
