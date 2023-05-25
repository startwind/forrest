<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint\File;

use Startwind\Forrest\Command\Parameters\Validation\Constraint\Constraint;
use Startwind\Forrest\Command\Parameters\Validation\SuccessfulValidationResult;
use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;

class FileExistsConstraint implements Constraint
{
    public static function validate(string $value): ValidationResult
    {
        if (!file_exists($value)) {
            return new ValidationResult(false, "Cannot find file '" . $value . "'.");
        } else {
            return new SuccessfulValidationResult();
        }
    }
}
