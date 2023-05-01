<?php

namespace Startwind\Forrest\Config;

class Config
{
    const PARAM_REPOSITORIES = 'repositories';

    private array $configArray;

    public function __construct(array $configArray)
    {
        $this->configArray = $configArray;
    }

    public function addRepository(string $key, array $repositoryConfig): void
    {
        $this->configArray[self::PARAM_REPOSITORIES][$key] = $repositoryConfig;
    }

    public function removeRepository(string $key): void
    {
        if (array_key_exists($key, $this->configArray[self::PARAM_REPOSITORIES])) {
            unset($this->configArray[self::PARAM_REPOSITORIES][$key]);
        }
    }

    public function getConfigArray(): array
    {
        return $this->configArray;
    }
}
