<?php

namespace Startwind\Forrest\Config;

use Startwind\Forrest\Command\Command;
use Symfony\Component\Yaml\Yaml;

class ConfigFileHandler
{
    public function __construct(
        private readonly string $configFilename,
        private readonly string $checksumFilename
    ) {
    }

    public function persistChecksum(Command $command, string $repositoryIdentifier): void
    {
        if (file_exists($this->checksumFilename)) {
            $checksums = json_decode(file_get_contents($this->checksumFilename), true);
            if (!$checksums) {
                $checksums = [];
            }
        } else {
            $checksums = [];
        }

        $checksums[$this->getChecksumIdentifier($command, $repositoryIdentifier)] = $command->getChecksum();
        file_put_contents($this->checksumFilename, json_encode($checksums));
    }

    public function hasChecksumChanged(Command $command, string $repositoryIdentifier): bool
    {
        if (!file_exists($this->checksumFilename)) {
            return true;
        }

        $checksums = json_decode(file_get_contents($this->checksumFilename), true);
        if (!array_key_exists($command->getName(), $checksums)) {
            return true;
        }

        $checksumIdentifier = $this->getChecksumIdentifier($command, $repositoryIdentifier);

        if (!array_key_exists($checksumIdentifier, $checksums)) {
            return true;
        }

        return $checksums[$this->getChecksumIdentifier($command, $repositoryIdentifier)] !== $command->getChecksum();
    }

    private function getChecksumIdentifier(Command $command, string $repositoryIdentifier): string
    {
        return $repositoryIdentifier . $command->getName();
    }

    public function dumpConfig(Config $config): void
    {
        file_put_contents($this->configFilename, Yaml::dump($config->getConfigArray(), 4));
    }

    public function parseConfig(): Config
    {
        $configArray = Yaml::parse(file_get_contents($this->configFilename));
        return new Config($configArray);
    }
}
