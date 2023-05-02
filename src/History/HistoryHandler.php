<?php

namespace Startwind\Forrest\History;

class HistoryHandler
{
    private string $historyFilename;

    public function __construct(string $historyFilename)
    {
        $this->historyFilename = $historyFilename;
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
        return file($this->historyFilename);
    }
}
