<?php

namespace Startwind\Forrest\Command\Parameters;

/**
 * This parameter configuration class handles parameters that are file names.
 */
class FileParameter extends Parameter
{
    private array $fileFormats = [];

    public function getFileFormats(): array
    {
        return $this->fileFormats;
    }

    public function setFileFormats(array $fileFormats): void
    {
        $this->fileFormats = $fileFormats;
    }
}
