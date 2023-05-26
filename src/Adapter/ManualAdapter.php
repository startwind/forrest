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
    public function getCommands(): array
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
}
