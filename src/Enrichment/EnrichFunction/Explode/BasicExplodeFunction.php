<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\Explode;

use Startwind\Forrest\Enrichment\EnrichFunction\ExplodeEnrichFunction;
use Startwind\Forrest\Enrichment\EnrichFunction\StringEnrichFunction;

abstract class BasicExplodeFunction implements ExplodeEnrichFunction
{
    protected string $functionName = '';

    public function applyFunction(string $string): string|array
    {
        if ($this->functionName == '') {
            throw new \RuntimeException('The function name must be set');
        }

        $pattern = '#' . preg_quote(ExplodeEnrichFunction::FUNCTION_LIMITER_START) . $this->functionName . '\((.*?)\)' . preg_quote(ExplodeEnrichFunction::FUNCTION_LIMITER_END) . '#';

        preg_match_all($pattern, $string, $matches);

        if (count($matches) > 0) {
            foreach ($matches[1] as $functionValue) {
                return $this->getValue($functionValue);
            }
        }

        return $string;
    }

    abstract protected function getValue(string $value): array;
}
