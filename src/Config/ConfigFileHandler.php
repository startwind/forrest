<?php

namespace Startwind\Forrest\Config;

use Startwind\Forrest\Command\Command;
use Symfony\Component\Yaml\Yaml;

class ConfigFileHandler
{
    private string $configFilename;
    private string $checksumFilename;

    public function __construct(string $configFilename, string $checksumFilename)
    {
        $this->configFilename = $configFilename;
        $this->checksumFilename = $checksumFilename;
    }

    public function persistChecksum(Command $command): void
    {
        if (file_exists($this->checksumFilename)) {
            $checksums = json_decode(file_get_contents($this->checksumFilename), true);
            if (!$checksums) {
                $checksums = [];
            }
        } else {
            $checksums = [];
        }

        $checksums[$command->getName()] = $command->getChecksum();
        file_put_contents($this->checksumFilename, json_encode($checksums));
    }

    public function hasChecksumChanged(Command $command): bool
    {
        if (!file_exists($this->checksumFilename)) {
            return true;
        }

        $checksums = json_decode(file_get_contents($this->checksumFilename), true);
        if (!array_key_exists($command->getName(), $checksums)) {
            return true;
        }

        return $checksums[$command->getName()] !== $command->getChecksum();
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
