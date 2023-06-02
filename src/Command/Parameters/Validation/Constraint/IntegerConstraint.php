<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint;

use Startwind\Forrest\Command\Parameters\Validation\SuccessfulValidationResult;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;

class IntegerConstraint implements Constraint
{
    public static function validate(string $value): ValidationResult
    {
        if (preg_match("/^[0-9]*$/", $value) === 0) {
            return new ValidationResult(false, 'The given value must be a number.');
        } else {
            return new SuccessfulValidationResult();
        }
    }
}
