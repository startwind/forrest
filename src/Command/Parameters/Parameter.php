<?php

namespace Startwind\Forrest\Command\Parameters;

use Startwind\Forrest\Command\Parameters\Validation\Constraint\NotEmptyConstraint;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;

class Parameter
{
    public const TYPE = 'mixed';

    private string $name = '';
    private string $description = '';
    private string $defaultValue = '';

    private array $values = [];

    private array $constraints = [
        NotEmptyConstraint::class
    ];

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function hasValues(): bool
    {
        return count($this->values) > 0;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * Check if the given value is valid.
     */
    public function validate(string $value): ValidationResult
    {
        foreach ($this->constraints as $constraint) {
            /** @var ValidationResult $constraintValidationResult */
            $constraintValidationResult = (call_user_func([$constraint, 'validate'], $value));
            if (!$constraintValidationResult->isValid()) {
                return $constraintValidationResult;
            }
        }
        return new ValidationResult(true);
    }
}
