<?php

namespace Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use Startwind\Forrest\Command\Command;

class ManualAdapter implements Adapter
{
    private const TYPE = 'manual';

    private array $commands = [];

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public static function fromConfigArray(array $config, Client $client): Adapter
    {
        return new self();
    }

    public function isEditable(): bool
    {
        return false;
    }

    public function addCommand(Command $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    public function removeCommand(string $commandName): void
    {
        unset($this->commands[$commandName]);
    }
}
