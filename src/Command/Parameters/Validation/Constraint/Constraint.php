<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint;

use Startwind\Forrest\Command\Parameters\Validation\ValidationResult;

interface Constraint
{
    public static function validate(string $value): ValidationResult;
}
