<?php

namespace Startwind\Forrest\Config;

class Config
{
    public const PARAM_REPOSITORIES = 'repositories';

    public function __construct(
        private array $configArray
    ) {
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
