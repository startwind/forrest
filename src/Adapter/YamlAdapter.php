<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;
use Symfony\Component\Yaml\Yaml;

class YamlAdapter implements Adapter, ClientAwareAdapter
{
    const TYPE = 'yaml';

    const YAML_FIELD_COMMANDS = 'commands';

    private Client $client;

    private string $yamlFile;

    public function __construct(string $yamlFile)
    {
        $this->yamlFile = $yamlFile;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
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

        foreach ($config[self::YAML_FIELD_COMMANDS] as $commandConfig) {
            $commands[] = new Command($commandConfig['name'], $commandConfig['description'], $commandConfig['command']);
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
