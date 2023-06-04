<?php

namespace Startwind\Forrest\Command\Parameters;

use Startwind\Forrest\Command\Parameters\Validation\Constraint\NotEmptyConstraint;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;
use Startwind\Forrest\Enrichment\EnrichFunction\Explode\FunctionComposite;

class Parameter implements \JsonSerializable
{
    public const PARAMETER_PREFIX = '${';
    public const PARAMETER_POSTFIX = '}';

    public const ENUM_CUSTOM_KEY = '##custom##';
    public const ENUM_CUSTOM = '<custom value>';

    public const TYPE = 'mixed';

    private string $name = '';
    private string $description = '';

    private string $prefix = '';
    private string $suffix = '';
    private string $defaultValue = '';

    private array $values = [];

    private array $rawStructure = [];

    private array $constraints = [
        NotEmptyConstraint::class
    ];

    /**
     * @param array $rawStructure
     */
    public function setRawStructure(array $rawStructure): void
    {
        $this->rawStructure = $rawStructure;
    }

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

    public function setValues(array|string $values, bool $allowCustomValue): void
    {
        if (is_array($values)) {
            $this->values = $values;
        } else {
            $explodeFunctionComposite = new FunctionComposite();
            $this->values = $explodeFunctionComposite->applyFunction($values);
        }

        if ($allowCustomValue) {
            if (array_is_list($this->values)) {
                array_unshift($this->values, self::ENUM_CUSTOM);
            } else {
                $this->values[self::ENUM_CUSTOM_KEY] = self::ENUM_CUSTOM;
            }
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

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     */
    public function setSuffix(string $suffix): void
    {
        $this->suffix = $suffix;
    }

    public function jsonSerialize(): array
    {
        return $this->rawStructure;
    }
}
