<?php

namespace Startwind\Forrest\History;

class HistoryHandler
{
    public function __construct(
        private readonly string $historyFilename
    )
    {
    }

    /**
     * Add a new line to the history.
     */
    public function addEntry(string $command): void
    {
        file_put_contents($this->historyFilename, $command . "\n", FILE_APPEND);
    }

    /**
     * Get the history entries as string[]
     */
    public function getEntries(): array
    {
        if (!is_file($this->historyFilename)) {
            return [];
        }

        return file($this->historyFilename);
    }
}
