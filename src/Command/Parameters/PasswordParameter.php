<?php

namespace Startwind\Forrest\Command\Parameters;

/**
 * This parameter configuration class handles parameters that are file names.
 */
class PasswordParameter extends Parameter
{
    public const TYPE = 'password';

    public function getType(): string
    {
        return self::TYPE;
    }
}
