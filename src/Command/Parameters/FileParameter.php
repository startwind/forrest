<?php

namespace Startwind\Forrest\Command\Parameters;

class FileParameter extends Parameter implements NameAwareParameter
{
    private string $name = '';
    private string $description = '';
    private array $fileFormats = [];

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param array $fileFormats
     */
    public function setFileFormats(array $fileFormats): void
    {
        $this->fileFormats = $fileFormats;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFileFormats(): array
    {
        return $this->fileFormats;
    }
}
