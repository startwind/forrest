<?php

namespace Startwind\Forrest\Command\Parameters;

use Startwind\Forrest\Command\Parameters\Validation\Constraint\NotEmptyConstraint;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;
use Startwind\Forrest\Enrichment\EnrichFunction\Explode\FunctionComposite;

class Parameter
{
    public const PARAMETER_PREFIX = '${';
    public const PARAMETER_POSTFIX = '}';

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

    public function setValues(array|string $values): void
    {
        if (is_array($values)) {
            $this->values = $values;
        } else {
            $explodeFunctionComposite = new FunctionComposite();
            $this->values = $explodeFunctionComposite->applyFunction($values);
        }
    }

    public function hasValues(): bool
    {
        return count($this->values) > 0;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function setConstraints(array $constraints): void
    {
        $this->constraints = $constraints;
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
