<?php

namespace Startwind\Forrest\Command\Parameters;

/**
 * This parameter configuration class handles parameters that are file names.
 */
class FileParameter extends Parameter
{
    public const DIRECTORY = 'directory';

    private array $fileFormats = [];

    public function setFileFormats(array $fileFormats): void
    {
        $this->fileFormats = $fileFormats;
    }

    /**
     * Return true if the given filename is compatible with this parameters
     * file formats.
     *
     * @var string[] $compatibleFilenames
     */
    public function isCompatibleWithFiles(array $compatibleFilenames): bool
    {
        $normalizedCompatibleFilenames = [];

        foreach ($compatibleFilenames as $compatibleFilename) {
            $normalizedCompatibleFilenames[] = strtolower($compatibleFilename);
        }

        foreach ($this->fileFormats as $fileFormat) {
            foreach ($normalizedCompatibleFilenames as $normalizedCompatibleFilename) {
                if (str_contains($normalizedCompatibleFilename, $fileFormat)) {
                    return true;
                }
            }
        }
        return false;
    }
}
