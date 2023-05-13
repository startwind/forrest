<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Adapter\Loader\HttpAwareLoader;
use Startwind\Forrest\Adapter\Loader\HttpFileLoader;
use Startwind\Forrest\Adapter\Loader\Loader;
use Startwind\Forrest\Adapter\Loader\LoaderFactory;
use Startwind\Forrest\Adapter\Loader\LocalFileLoader;
use Startwind\Forrest\Adapter\Loader\WritableLoader;
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

    public const YAML_FIELD_OUTPUT = 'output-format';

    public function __construct(private readonly Loader $loader)
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
        $content = $this->loader->load();
        if (!$content) {
            return [];
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
            throw new \RuntimeException('The given YAML file does not contain a section named "' . self::YAML_FIELD_COMMANDS . '".');
        }

        foreach ($config[self::YAML_FIELD_COMMANDS] as $identifier => $commandConfig) {
            if (!array_key_exists(self::YAML_FIELD_PROMPT, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_PROMPT . ' is not set for identifier "' . $identifier . '".');
            }
            if (!array_key_exists(self::YAML_FIELD_DESCRIPTION, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_DESCRIPTION . ' is not set for identifier "' . $identifier . '".');
            }

            $prompt = $commandConfig[self::YAML_FIELD_PROMPT];

            $command = new Command($commandConfig[self::YAML_FIELD_NAME], $commandConfig[self::YAML_FIELD_DESCRIPTION], $prompt);

            if (array_key_exists(self::YAML_FIELD_OUTPUT, $commandConfig)) {
                $command->setOutputFormat($commandConfig[self::YAML_FIELD_OUTPUT]);
            }

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
        if (array_key_exists('file', $config)) {
            $yamlFile = $config['file'];
            if (str_contains($yamlFile, '://')) {
                $loader = new HttpFileLoader($yamlFile);
            } else {
                $loader = new LocalFileLoader($yamlFile);
            }
        } elseif (array_key_exists('loader', $config)) {
            $loader = LoaderFactory::create($config['loader']);
        } else {
            throw new \RuntimeException('Configuration not applicable.');
        }
        return new self($loader);
    }

    /**
     * @inheritDoc
     */
    public function isEditable(): bool
    {
        return $this->loader instanceof WritableLoader;
    }

    /**
     * @inheritDoc
     */
    public function addCommand(Command $command): void
    {
        if (!$this->loader instanceof WritableLoader) {
            throw new \RuntimeException('This repository is not writable.');
        }

        $config = Yaml::parse($this->loader->load());

        foreach ($config[self::YAML_FIELD_COMMANDS] as $commandConfig) {
            if ($commandConfig[self::YAML_FIELD_NAME] == $command->getName()) {
                throw new \RuntimeException('A command with the name "' . $command->getName() . '" already exists. Please choose another one or delete the old command first.');
            }
        }

        $commandArray = [
            self::YAML_FIELD_NAME => $command->getName(),
            self::YAML_FIELD_DESCRIPTION => $command->getDescription(),
            self::YAML_FIELD_PROMPT => $command->getPrompt(),
        ];

        $commandName = $this->convertNameToIdentifier($command->getName());

        $config[YamlAdapter::YAML_FIELD_COMMANDS][$commandName] = $commandArray;

        $this->loader->write(Yaml::dump($config, 4));
    }

    /**
     * @inheritDoc
     */
    public function removeCommand(string $commandName): void
    {
        if (!($this->loader instanceof WritableLoader)) {
            throw new \RuntimeException('This repository is not editable.');
        }

        $config = Yaml::parse($this->loader->load());

        $changed = false;

        foreach ($config[self::YAML_FIELD_COMMANDS] as $key => $commandConfig) {
            if ($commandConfig[self::YAML_FIELD_NAME] == $commandName) {
                unset($config[self::YAML_FIELD_COMMANDS][$key]);
                $changed = true;
            }
        }

        if (!$changed) {
            throw  new \RuntimeException('No command with nane "' . $commandName . '" found.');
        }

        $this->loader->write(Yaml::dump($config, 4));
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
        if ($this->loader instanceof HttpAwareLoader) {
            $this->loader->setClient($client);
        }
    }
}
