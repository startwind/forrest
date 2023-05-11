<?php

namespace Startwind\Forrest\Enrichment\Function;

interface EnrichFunction
{
    public const FUNCTION_LIMITER_START = '${';
    public const FUNCTION_LIMITER_END = '}';

    public function applyFunction(string $string): string;
}
