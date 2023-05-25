<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint;

use Startwind\Forrest\Command\Parameters\Validation\SuccessfulValidationResult;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;

class IdentifierConstraint implements Constraint
{
    public static function validate(string $value): ValidationResult
    {
        if (preg_match("/^[0-9a-z]*$/", $value) === 0) {
            return new ValidationResult(false, 'The given value must only contain numbers and lower case characters.');
        } else {
            return new SuccessfulValidationResult();
        }
    }
}
