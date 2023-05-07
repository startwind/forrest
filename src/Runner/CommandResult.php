<?php

namespace Startwind\Forrest\Runner;

class CommandResult
{
    private array $output;
    private string $resultCode;

    public function __construct(array $output, string $resultCode)
    {
        $this->output = $output;
        $this->resultCode = $resultCode;
    }

    public function getOutput(): array
    {
        return $this->output;
    }

    public function getResultCode(): string
    {
        return $this->resultCode;
    }
}
