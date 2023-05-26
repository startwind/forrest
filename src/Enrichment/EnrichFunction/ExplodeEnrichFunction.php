<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction;

interface ExplodeEnrichFunction
{
    public const FUNCTION_LIMITER_START = StringEnrichFunction::FUNCTION_LIMITER_START;
    public const FUNCTION_LIMITER_END = StringEnrichFunction::FUNCTION_LIMITER_END;

    public function applyFunction(string $string): array;
}
