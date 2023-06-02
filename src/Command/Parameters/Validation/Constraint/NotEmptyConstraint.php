<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint;

use Startwind\Forrest\Command\Parameters\Validation\SuccessfulValidationResult;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;

class NotEmptyConstraint implements Constraint
{
    public static function validate(string $value): ValidationResult
    {
        if (strlen($value) === 0) {
            return new ValidationResult(false, 'The given value must not be empty.');
        } else {
            return new SuccessfulValidationResult();
        }
    }
}
