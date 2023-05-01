<?php

namespace Startwind\Forrest\Config;

use Symfony\Component\Yaml\Yaml;

class ConfigFileHandler
{
    private string $configFilename;

    public function __construct(string $configFilename)
    {
        $this->configFilename = $configFilename;
    }

    public function dump(Config $config): void
    {
        file_put_contents($this->configFilename, Yaml::dump($config->getConfigArray()));
    }

    public function parse(): Config
    {
        $configArray = Yaml::parse(file_get_contents($this->configFilename));
        return new Config($configArray);
    }
}
