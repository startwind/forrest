<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Adapter\Adapter;

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


}
