<?php

namespace Startwind\Forrest\Command\Parameters;

/**
 * This parameter configuration class handles parameters that are file names.
 */
class FileParameter extends Parameter implements NameAwareParameter
{
    private string $name = '';
    private string $description = '';
    private array $fileFormats = [];

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setFileFormats(array $fileFormats): void
    {
        $this->fileFormats = $fileFormats;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFileFormats(): array
    {
        return $this->fileFormats;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
