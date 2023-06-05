<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint;

use Startwind\Forrest\Command\Parameters\Validation\SuccessfulValidationResult;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;

class MacConstraint implements Constraint
{
    public static function validate(string $value): ValidationResult
    {
        if (filter_var($value, FILTER_VALIDATE_MAC)) {
            return new SuccessfulValidationResult();
        } else {
            return new ValidationResult(false, 'The given value must by a valid MAC address.');
        }
    }
}
