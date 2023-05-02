<?php

namespace Startwind\Forrest\Adapter;

use Startwind\Forrest\Command\Command;
use Symfony\Component\Yaml\Yaml;

class YamlAdapter implements Adapter
{
    const TYPE = 'yaml';

    const YAML_FIELD_COMMANDS = 'commands';
    const YAML_FIELD_PROMPT = 'prompt';
    const YAML_FIELD_DESCRIPTION = 'description';

    private string $yamlFile;

    public function __construct(string $yamlFile)
    {
        $this->yamlFile = $yamlFile;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @inheritDoc
     */
    public function getCommands(): array
    {
        $config = Yaml::parse(file_get_contents($this->yamlFile));

        $commands = [];

        foreach ($config[self::YAML_FIELD_COMMANDS] as $identifier => $commandConfig) {
            if (!array_key_exists(self::YAML_FIELD_PROMPT, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_PROMPT . ' is not set for identifier "' . $identifier . '" (file: ' . $this->yamlFile . ').');
            }
            if (!array_key_exists(self::YAML_FIELD_DESCRIPTION, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_DESCRIPTION . ' is not set for identifier "' . $identifier . '" (file: ' . $this->yamlFile . ').');
            }
            $commands[] = new Command($commandConfig['name'], $commandConfig[self::YAML_FIELD_DESCRIPTION], $commandConfig[self::YAML_FIELD_PROMPT]);
        }

        return $commands;
    }

    /**
     * @inheritDoc
     */
    static public function fromConfigArray(array $config): YamlAdapter
    {
        return new self($config['file']);
    }
}
