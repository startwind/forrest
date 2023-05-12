<?php

namespace Startwind\Forrest\Adapter\Loader;

use Startwind\Forrest\Adapter\YamlAdapter;
use Symfony\Component\Yaml\Yaml;

class LocalFileLoader implements Loader, WritableLoader
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @inheritDoc
     */
    public function load(): string
    {
        return file_get_contents($this->filename);
    }

    /**
     * @inheritDoc
     */
    public static function fromConfigArray(array $config): Loader
    {
        return new self($config['file']);
    }

    /**
     * @inheritDoc
     */
    public function addCommand(string $commandName, array $command): void
    {
        $config = Yaml::parse(file_get_contents($this->filename));
        $config[YamlAdapter::YAML_FIELD_COMMANDS][$commandName] = $command;
        file_put_contents($this->filename, Yaml::dump($config, 2));
    }

    /**
     * @inheritDoc
     */
    public function removeCommand(string $commandName): void
    {
        $config = Yaml::parse($this->filename);

        foreach ($config[YamlAdapter::YAML_FIELD_COMMANDS] as $key => $commandConfig) {
            if ($commandConfig[YamlAdapter::YAML_FIELD_NAME] == $commandName) {
                unset($config[YamlAdapter::YAML_FIELD_COMMANDS][$key]);
            }
        }

        file_put_contents($this->filename, Yaml::dump($config, 2));
    }

}
