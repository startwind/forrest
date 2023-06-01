<?php

namespace Startwind\Forrest\Repository\File;

use Startwind\Forrest\Adapter\Adapter;
use Startwind\Forrest\Adapter\ListAwareAdapter;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\FileParameter;
use Startwind\Forrest\Logger\ForrestLogger;
use Startwind\Forrest\Repository\ListAware;
use Startwind\Forrest\Repository\Repository;
use Startwind\Forrest\Repository\SearchAware;
use Startwind\Forrest\Runner\CommandRunner;
use function Startwind\Forrest\Repository\str_contains;

class FileRepository implements Repository, SearchAware, ListAware
{
    private array $commands = [];

    private bool $fetchedWithParameters = false;

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
    public function getCommands(bool $withParameters = true): array
    {
        /** @var ListAwareAdapter $adapter */
        $adapter = $this->getAdapter();

        if (!$this->commands || (!$this->fetchedWithParameters && $withParameters)) {
            try {
                $this->commands = $adapter->getCommands($withParameters);
            } catch (\Exception $exception) {
                ForrestLogger::error('Unable to get commands: ' . $exception->getMessage() . '.');
                return [];
            }
        }

        $this->fetchedWithParameters = $this->fetchedWithParameters || $withParameters;

        return $this->commands;
    }

    /**
     * @inheritDoc
     */
    public function hasCommands(): bool
    {
        return count($this->getCommands(false)) > 0;
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

        foreach ($this->getCommands(true) as $command) {
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

        foreach ($this->getCommands(false) as $command) {
            foreach ($tools as $tool) {
                if (CommandRunner::extractToolFromPrompt($command->getPrompt()) == $tool) {
                    $commands[] = $command;
                }
            }
        }
        return $commands;
    }

    /**
     * @throws \Exception
     */
    public function getCommand(string $identifier): Command
    {
        $commands = $this->getCommands(true);

        foreach ($commands as $command) {
            if ($command->getName() == $identifier) {
                return $command;
            }
        }

        throw new \RuntimeException('No command with name "' . $identifier . '" found.');
    }

    /**
     * @inheritDoc
     */
    public function assertHealth(): void
    {
        $this->getAdapter()->assertHealth();
    }
}
