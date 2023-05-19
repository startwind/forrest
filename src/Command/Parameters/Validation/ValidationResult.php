<?php

namespace Startwind\Forrest\Command\Parameters\Validation;

class ValidationResult
{
    private bool $isValid;
    private string $validationMessage;

    public function __construct(bool $isValid, string $validationMessage = '')
    {
        $this->isValid = $isValid;
        $this->validationMessage = $validationMessage;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getValidationMessage(): string
    {
        return $this->validationMessage;
    }
}
