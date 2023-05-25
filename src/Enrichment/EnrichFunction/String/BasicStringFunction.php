<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\String;

use Startwind\Forrest\Enrichment\EnrichFunction\StringEnrichFunction;

abstract class BasicStringFunction implements StringEnrichFunction
{
    protected string $functionName = '';

    public function applyFunction(string $string): string
    {
        if ($this->functionName == '') {
            throw new \RuntimeException('The function name must be set');
        }

        $pattern = '#' . preg_quote(StringEnrichFunction::FUNCTION_LIMITER_START) . $this->functionName . '\((.*?)\)' . preg_quote(StringEnrichFunction::FUNCTION_LIMITER_END) . '#';
        preg_match_all($pattern, $string, $matches);
        if (count($matches) > 0) {
            foreach ($matches[1] as $functionValue) {
                $string = str_replace(StringEnrichFunction::FUNCTION_LIMITER_START . $this->functionName . '(' . $functionValue . ')' . StringEnrichFunction::FUNCTION_LIMITER_END, $this->getValue($functionValue), $string);
            }
        }
        return $string;
    }

    abstract protected function getValue(string $value): string;
}
