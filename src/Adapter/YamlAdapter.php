<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Startwind\Forrest\Adapter\Exception\RepositoryNotFoundException;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\ParameterFactory;
use Symfony\Component\Yaml\Yaml;

class YamlAdapter extends BasicAdapter implements ClientAwareAdapter
{
    public const TYPE = 'yaml';

    public const YAML_FIELD_COMMANDS = 'commands';
    public const YAML_FIELD_PROMPT = 'prompt';
    public const YAML_FIELD_NAME = 'name';
    public const YAML_FIELD_DESCRIPTION = 'description';
    public const YAML_FIELD_RUNNABLE = 'runnable';
    public const YAML_FIELD_PARAMETERS = 'parameters';

    private Client $client;

    public function __construct(private readonly string $yamlFile)
    {
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
            if (!file_exists($this->yamlFile)) {
                throw new RepositoryNotFoundException('Unable to open ' . $this->yamlFile . '. File not found.');
            }
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

        if (!array_key_exists(self::YAML_FIELD_COMMANDS, $config)) {
            var_dump(array_keys($config));
            var_dump($this->yamlFile);
            throw new \RuntimeException('The given YAML file does not contain a section named "' . self::YAML_FIELD_COMMANDS . '".');
        }

        foreach ($config[self::YAML_FIELD_COMMANDS] as $identifier => $commandConfig) {
            if (!array_key_exists(self::YAML_FIELD_PROMPT, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_PROMPT . ' is not set for identifier "' . $identifier . '" (file: ' . $this->yamlFile . ').');
            }
            if (!array_key_exists(self::YAML_FIELD_DESCRIPTION, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_DESCRIPTION . ' is not set for identifier "' . $identifier . '" (file: ' . $this->yamlFile . ').');
            }

            $prompt = $commandConfig[self::YAML_FIELD_PROMPT];

            $command = new Command($commandConfig[self::YAML_FIELD_NAME], $commandConfig[self::YAML_FIELD_DESCRIPTION], $prompt);

            if (array_key_exists(self::YAML_FIELD_PARAMETERS, $commandConfig)) {
                $parameterConfig = $commandConfig[self::YAML_FIELD_PARAMETERS];
            } else {
                $parameterConfig = [];
            }

            if (is_string($parameterConfig)) {
                throw new \RuntimeException('The configuration is malformed. Array expected but "' . $parameterConfig . '" found.');
            }

            $command->setParameters($this->createParameters($prompt, $parameterConfig));

            if (array_key_exists(self::YAML_FIELD_RUNNABLE, $commandConfig)) {
                if ($commandConfig[self::YAML_FIELD_RUNNABLE] === false) {
                    $command->flagAsNotRunnable();
                }
            }

            $commands[] = $command;
        }

        return $commands;
    }

    /**
     * @return \Startwind\Forrest\Command\Parameters\Parameter[]
     */
    protected function createParameters(string $prompt, array $parameterConfig): array
    {
        $parameterNames = $this->extractParametersFromPrompt($prompt);

        $parameters = [];

        foreach ($parameterNames as $parameterName) {
            if (array_key_exists($parameterName, $parameterConfig)) {
                $config = $parameterConfig[$parameterName];
            } else {
                $config = [];
            }
            $parameters[$parameterName] = ParameterFactory::create($config);
        }

        return $parameters;
    }

    /**
     * @inheritDoc
     */
    public static function fromConfigArray(array $config): Adapter
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
            self::YAML_FIELD_PROMPT => $command->getPrompt(),
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

    /**
     * @inheritDoc
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
