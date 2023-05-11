<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction;

abstract class BasicFunction implements EnrichFunction
{
    protected string $functionName = '';

    public function applyFunction(string $prompt): string
    {
        if ($this->functionName == '') {
            throw new \RuntimeException('The function name must be set');
        }

        $pattern = '#' . preg_quote(EnrichFunction::FUNCTION_LIMITER_START) . $this->functionName . '\((.*?)\)' . preg_quote(EnrichFunction::FUNCTION_LIMITER_END) . '#';
        preg_match_all($pattern, $prompt, $matches);
        if (count($matches) > 0) {
            foreach ($matches[1] as $dateFormat) {
                $prompt = str_replace(EnrichFunction::FUNCTION_LIMITER_START . $this->functionName . '(' . $dateFormat . ')' . EnrichFunction::FUNCTION_LIMITER_END, $this->getValue($dateFormat), $prompt);
            }
        }
        return $prompt;
    }

    abstract protected function getValue(string $value): string;
}
