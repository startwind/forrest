<?php

namespace Startwind\Forrest\Command\Parameters\Validation;

class SuccessfulValidationResult extends ValidationResult
{
    public function __construct()
    {
        parent::__construct(true);
    }
}
