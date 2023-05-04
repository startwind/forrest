<?php

namespace Startwind\Forrest\Command\Parameters;
class Config
{
    private string $name;
    private string $description;
    private string $type;
    private string $fileFormats;

    public function __construct(string $name, string $description, string $type, string $fileFormats)
    {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->fileFormats = $fileFormats;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFileFormats(): string
    {
        return $this->fileFormats;
    }
}
