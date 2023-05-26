<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;

class ManualAdapter implements Adapter, ListAwareAdapter, EditableAdapter
{
    private const TYPE = 'manual';

    private array $commands = [];

    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @inheritDoc
     */
    public function getCommands(bool $withParameters = true): array
    {
        return $this->commands;
    }

    public static function fromConfigArray(array $config, Client $client): Adapter
    {
        return new self();
    }

    /**
     * @inheritDoc
     */
    public function isEditable(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function addCommand(Command $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * @inheritDoc
     */
    public function removeCommand(string $commandName): void
    {
        unset($this->commands[$commandName]);
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
    }
}
