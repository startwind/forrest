<?php

namespace Startwind\Forrest\CliCommand\Directory\Exception;

class MultiException extends \RuntimeException
{
    /**
     * @var \Exception[]
     */
    private array $exceptions = [];

    public function addException(\Exception $exception)
    {
        $this->exceptions[] = $exception;
    }

    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    public function hasExceptions(): bool
    {
        return count($this->exceptions) > 0;
    }
}
