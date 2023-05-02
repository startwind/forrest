<?php

namespace Startwind\Forrest\History;

class HistoryHandler
{
    private string $historyFilename;

    public function __construct(string $historyFilename)
    {
        $this->historyFilename = $historyFilename;
    }

    public function addEntry(string $command): void
    {
        file_put_contents($this->historyFilename, $command . "\n", FILE_APPEND);
    }

    public function getEntries(): array
    {
        return file($this->historyFilename);
    }
}
