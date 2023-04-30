<?php

namespace Startwind\Forrest\Command;

class Command
{
    private string $command;
    private string $name;
    private string $description;

    /**
     * @param string $command
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description, string $command)
    {
        $this->command = $command;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
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
}
