<?php

namespace Startwind\Forrest\Command\Tool;

class Tool
{
    private string $name;
    private string $description;
    private string $see = '';

    /**
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getSee(): string
    {
        return $this->see;
    }

    /**
     * @param string $see
     */
    public function setSee(string $see): void
    {
        $this->see = $see;
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
