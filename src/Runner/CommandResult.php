<?php

namespace Startwind\Forrest\Runner;

class CommandResult
{
    private array $output;
    private string $resultCode;

    public function __construct(array $output, int $resultCode)
    {
        $this->output = $output;
        $this->resultCode = $resultCode;
    }

    public function getOutput(): array
    {
        return $this->output;
    }

    public function getResultCode(): int
    {
        return $this->resultCode;
    }
}
