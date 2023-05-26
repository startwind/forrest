<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Adapter\Adapter;
use Startwind\Forrest\Command\Parameters\FileParameter;
use Startwind\Forrest\Runner\CommandRunner;

class FileRepository implements Repository, SearchAware, ListAware
{
    private array $commands = [];

    public function __construct(
        private readonly Adapter $adapter,
        private readonly string  $name,
        private readonly string  $description,
        private readonly bool    $isSpecialRepo = false,
    )
    {
    }

    /**
     * Return the adapter to child classes.
     */
    protected function getAdapter(): Adapter
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getCommands(): array
    {
        $exceptions = [];

        if (!$this->commands) {
            try {
                $this->commands = $this->adapter->getCommands();
            } catch (\Exception $exception) {
                $exceptions[] = $exception;
                throw $exception;
            }
        }

        return $this->commands;
    }

    /**
     * @inheritDoc
     */
    public function hasCommands(): bool
    {
        return count($this->getCommands()) > 0;
    }

    /**
     * @inheritDoc
     */
    public function isSpecial(): bool
    {
        return $this->isSpecialRepo;
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function searchByFile(array $files): array
    {
        $commands = [];

        foreach ($this->getCommands() as $command) {
            $parameters = $command->getParameters();
            foreach ($parameters as $parameter) {
                if ($parameter instanceof FileParameter) {
                    if ($parameter->isCompatibleWithFiles($files)) {
                        $commands[] = $command;
                        continue(2);
                    }
                }
            }
        }

        return $commands;
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function searchByPattern(array $patterns): array
    {
        $commands = [];

        foreach ($this->getCommands() as $command) {
            foreach ($patterns as $pattern) {
                if (str_contains(strtolower($command->getName()), strtolower($pattern))) {
                    $commands[] = $command;
                } elseif (str_contains(strtolower($command->getDescription()), strtolower($pattern))) {
                    $commands[] = $command;
                }
            }
        }

        return $commands;
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function searchByTools(array $tools): array
    {
        $commands = [];

        foreach ($this->getCommands() as $command) {
            foreach ($tools as $tool) {
                if (CommandRunner::extractToolFromPrompt($command->getPrompt()) == $tool) {
                    $commands[] = $command;
                }
            }
        }
        return $commands;
    }
}
