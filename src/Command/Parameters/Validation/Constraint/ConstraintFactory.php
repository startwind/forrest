<?php

namespace Startwind\Forrest\Command\Parameters\Validation\Constraint;

use Startwind\Forrest\Command\Parameters\Validation\Constraint\File\FileExistsConstraint;
use Startwind\Forrest\Command\Parameters\Validation\Constraint\File\FileNotExistsConstraint;

abstract class ConstraintFactory
{
    private static array $validConstraints = [
        'integer' => IntegerConstraint::class,
        'identifier' => IdentifierConstraint::class,
        'not-empty' => NotEmptyConstraint::class,
        'url' => UrlConstraint::class,
        'ip-address' => IpConstraint::class,
        'mac-address' => MacConstraint::class,
        'email' => EmailConstraint::class,
        # File constraints
        'file-exists' => FileExistsConstraint::class,
        'file-not-exists' => FileNotExistsConstraint::class,
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
