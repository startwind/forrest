<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction;

class DateFunction extends BasicFunction
{
    protected string $functionName = 'date';

    protected function getValue(string $value): string
    {
        return date($value);
    }
}
