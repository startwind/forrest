<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Startwind\Forrest\Adapter\Exception\RepositoryNotFoundException;
use Startwind\Forrest\Command\Command;
use Symfony\Component\Yaml\Yaml;

class YamlAdapter implements Adapter, ClientAwareAdapter
{
    const TYPE = 'yaml';

    const YAML_FIELD_COMMANDS = 'commands';
    const YAML_FIELD_PROMPT = 'prompt';
    const YAML_FIELD_NAME = 'name';
    const YAML_FIELD_DESCRIPTION = 'description';

    private string $yamlFile;

    private Client $client;

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

    private function getConfig(): array
    {
        if (str_contains($this->yamlFile, '://')) {
            try {
                $response = $this->client->get($this->yamlFile);
            } catch (ClientException $exception) {
                if ($exception->getResponse()->getStatusCode() === 404) {
                    throw new RepositoryNotFoundException("The given repository can't be found.");
                } else {
                    throw $exception;
                }
            }
            $content = (string)$response->getBody();
        } else {
            $content = file_get_contents($this->yamlFile);
        }

        return Yaml::parse($content);
    }

    /**
     * @inheritDoc
     */
    public function getCommands(): array
    {
        $config = $this->getConfig();

        $commands = [];

        foreach ($config[self::YAML_FIELD_COMMANDS] as $identifier => $commandConfig) {
            if (!array_key_exists(self::YAML_FIELD_PROMPT, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_PROMPT . ' is not set for identifier "' . $identifier . '" (file: ' . $this->yamlFile . ').');
            }
            if (!array_key_exists(self::YAML_FIELD_DESCRIPTION, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_DESCRIPTION . ' is not set for identifier "' . $identifier . '" (file: ' . $this->yamlFile . ').');
            }
            $commands[] = new Command($commandConfig[self::YAML_FIELD_NAME], $commandConfig[self::YAML_FIELD_DESCRIPTION], $commandConfig[self::YAML_FIELD_PROMPT]);
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

    /**
     * @inheritDoc
     */
    public function isEditable(): bool
    {
        return !str_contains($this->yamlFile, '://');
    }

    /**
     * @inheritDoc
     */
    public function addCommand(Command $command): void
    {
        if (!$this->isEditable()) {
            throw new \RuntimeException('This repository is not editable.');
        }

        $config = Yaml::parse(file_get_contents($this->yamlFile));

        foreach ($config[self::YAML_FIELD_COMMANDS] as $commandConfig) {
            if ($commandConfig[self::YAML_FIELD_NAME] == $command->getName()) {
                throw new \RuntimeException('A command with the name "' . $command->getName() . '" already exists. Please choose another one or delete the old command first.');
            }
        }

        $config[self::YAML_FIELD_COMMANDS][$command->getName()] = [
            self::YAML_FIELD_NAME => $this->convertNameToIdentifier($command->getName()),
            self::YAML_FIELD_DESCRIPTION => $command->getDescription(),
            self::YAML_FIELD_PROMPT => $command->getPrompt()
        ];

        file_put_contents($this->yamlFile, Yaml::dump($config, 2));
    }

    /**
     * @inheritDoc
     */
    public function removeCommand(string $commandName): void
    {
        if (!$this->isEditable()) {
            throw new \RuntimeException('This repository is not editable.');
        }

        $config = Yaml::parse(file_get_contents($this->yamlFile));

        foreach ($config[self::YAML_FIELD_COMMANDS] as $key => $commandConfig) {
            if ($commandConfig[self::YAML_FIELD_NAME] == $commandName) {
                unset($config[self::YAML_FIELD_COMMANDS][$key]);
            }
        }

        file_put_contents($this->yamlFile, Yaml::dump($config, 2));
    }


    /**
     * Return a valid identifier based on the command name.
     */
    private function convertNameToIdentifier(string $name): string
    {
        return strtolower(str_replace(' ', '_', $name));
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
