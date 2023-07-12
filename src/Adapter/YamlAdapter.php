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
use Startwind\Forrest\Command\CommandFactory;
use Startwind\Forrest\Command\Parameters\ParameterFactory;
use Symfony\Component\Yaml\Yaml;

class YamlAdapter extends BasicAdapter implements ClientAwareAdapter, ListAwareAdapter, EditableAdapter
{
    public const TYPE = 'yaml';

    public const YAML_FIELD_COMMANDS = 'commands';
    public const YAML_FIELD_PROMPT = 'prompt';
    public const YAML_FIELD_NAME = 'name';
    public const YAML_FIELD_DESCRIPTION = 'description';

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
    public function getCommands(bool $withParameters = true): array
    {
        $config = $this->getConfig();

        $commands = [];

        if (!array_key_exists(self::YAML_FIELD_COMMANDS, $config)) {
            throw new \RuntimeException('The given YAML file does not contain a section named "' . self::YAML_FIELD_COMMANDS . '".');
        }

        if (is_null($config[self::YAML_FIELD_COMMANDS])) {
            return [];
        }

        foreach ($config[self::YAML_FIELD_COMMANDS] as $identifier => $commandConfig) {
            if (!array_key_exists(self::YAML_FIELD_PROMPT, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_PROMPT . ' is not set for identifier "' . $identifier . '".');
            }
            if (!array_key_exists(self::YAML_FIELD_DESCRIPTION, $commandConfig)) {
                throw new \RuntimeException('The mandatory field ' . self::YAML_FIELD_DESCRIPTION . ' is not set for identifier "' . $identifier . '".');
            }

            $commands[$commandConfig[self::YAML_FIELD_NAME]] = CommandFactory::fromArray($commandConfig, $withParameters);
        }

        return $commands;
    }

    /**
     * @inheritDoc
     */
    public static function fromConfigArray(array $config, Client $client): Adapter
    {
        if (array_key_exists('file', $config)) {
            $yamlFile = $config['file'];
            $adapterConfig = ['config' => ['file' => $yamlFile]];
            if (str_contains($yamlFile, '://')) {
                $adapterConfig['type'] = 'httpFile';
            } else {
                $adapterConfig['type'] = 'localFile';
            }
        } elseif (array_key_exists('loader', $config)) {
            $adapterConfig = $config['loader'];
        } else {
            throw new \RuntimeException('Configuration not applicable.');
        }

        $loader = LoaderFactory::create($adapterConfig, $client);

        $adapter = new self($loader);
        $adapter->setClient($client);

        return $adapter;
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

    public function getCommand(string $identifier): Command
    {
        $commands = $this->getCommands();

        if (!array_key_exists($identifier, $commands)) {
            throw new \RuntimeException('No command with name ' . $identifier . ' found.');
        }

        return $commands[$identifier];
    }

    /**
     * @inheritDoc
     */
    public function assertHealth(): void
    {
        $this->loader->assertHealth();
    }
}
