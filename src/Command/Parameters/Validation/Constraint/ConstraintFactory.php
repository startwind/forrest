<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint;

abstract class ConstraintFactory
{
    private static array $validConstraints = [
        'integer' => IntegerConstraint::class,
        'not-empty' => NotEmptyConstraint::class
    ];

    public static function getConstraint(string $constraintName): string
    {
        $constraint = strtolower($constraintName);

        if (array_key_exists($constraint, self::$validConstraints)) {
            return self::$validConstraints[$constraint];
        } else {
            throw new \RuntimeException('The given constraint "' . $constraintName . '" was not found. Valid constraints are: ' . implode(', ', array_keys(self::$validConstraints)) . '.');
        }
    }
}
