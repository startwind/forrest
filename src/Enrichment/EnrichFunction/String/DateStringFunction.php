<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\String;

class DateStringFunction extends BasicStringFunction
{
    protected string $functionName = 'date';

    protected function getValue(string $value): string
    {
        return date($value);
    }
}
