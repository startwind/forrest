<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction;

class EnvFunction extends BasicFunction
{
    protected string $functionName = 'env';

    protected function getValue(string $value): string
    {
        $envVars = getenv();
        if (array_key_exists($value, $envVars)) {
            return $envVars[$value];
        } else {
            return '';
        }
    }

}
