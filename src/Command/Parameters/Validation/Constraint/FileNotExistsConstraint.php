<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint;

use Startwind\Forrest\Command\Parameters\Validation\SuccessfulValidationResult;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;

class FileNotExistsConstraint implements Constraint
{
    public static function validate(string $value): ValidationResult
    {
        if (file_exists($value)) {
            return new ValidationResult(false, "The file '" . $value . "' already exists.");
        } else {
            return new SuccessfulValidationResult();
        }
    }
}
