<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Adapter\Adapter;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\FileParameter;

class FileRepository implements Repository, SearchAwareRepository
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

    public function getAdapter(): Adapter
    {
        return $this->adapter;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Command[]
     * @throws \Exception
     */
    public function getCommands(): array
    {
        $exceptions = [];

        if (!$this->commands) {
            try {
                $this->commands = $this->adapter->getCommands();
            } catch (\Exception $exception) {
                $exceptions[] = $exception;
                throw  $exception;
            }
        }

        return $this->commands;
    }

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

    public static function createUniqueCommandName(string $repositoryIdentifier, Command $command): string
    {
        return $repositoryIdentifier . ':' . $command->getName();
    }

    /**
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

    public function searchByPattern(array $patterns): array
    {
        // TODO: Implement searchByPattern() method.
    }

    public function searchByTools(array $tools): array
    {
        // TODO: Implement searchByTools() method.
    }


}
