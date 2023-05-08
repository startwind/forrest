<?php

namespace Startwind\Forrest\Config;

class RecentParameterMemory
{
    private string $memoryFile;

    private array $memories = [];

    /**
     * @param string $memoryFile
     */
    public function __construct(string $memoryFile)
    {
        $this->memoryFile = $memoryFile;

        if (file_exists($memoryFile)) {
            $this->memories = json_decode(file_get_contents($memoryFile), true);
        }
    }

    public function addParameter(string $parameterIdentifier, string $value): void
    {
        $this->memories[$parameterIdentifier] = $value;
    }

    public function hasParameter(string $parameterIdentifier): bool
    {
        return array_key_exists($parameterIdentifier, $this->memories);
    }

    public function getParameter(string $parameterIdentifier): string
    {
        if (!$this->hasParameter($parameterIdentifier)) {
            throw new \RuntimeException('No parameter with identifier "' . $parameterIdentifier . '" found. Please use hasParameter before using this method.');
        }

        return $this->memories[$parameterIdentifier];
    }

    public function dump(): void
    {
        file_put_contents($this->memoryFile, json_encode($this->memories));
    }
}
