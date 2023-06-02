<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\Explode;

use Startwind\Forrest\Enrichment\EnrichFunction\CacheableFunction;
use Startwind\Forrest\Enrichment\EnrichFunction\ExplodeEnrichFunction;

abstract class BasicExplodeFunction implements ExplodeEnrichFunction
{
    protected string $functionName = '';

    private array $cachedValues = [];

    public function applyFunction(string $string): array|string
    {
        if ($this->functionName == '') {
            throw new \RuntimeException('The function name must be set');
        }

        $pattern = '#' . preg_quote(ExplodeEnrichFunction::FUNCTION_LIMITER_START) . $this->functionName . '\((.*?)\)' . preg_quote(ExplodeEnrichFunction::FUNCTION_LIMITER_END) . '#';

        preg_match_all($pattern, $string, $matches);

        if (count($matches) > 0) {
            foreach ($matches[1] as $functionValue) {
                $key = md5(get_class($this) . $functionValue);

                if (array_key_exists($key, $this->cachedValues)) {
                    return $this->cachedValues[$key];
                }

                $value = $this->getValue($functionValue);

                if ($this instanceof CacheableFunction) {
                    $this->cachedValues[$key] = $value;
                }

                return $value;
            }
        }

        return $string;
    }

    abstract protected function getValue(string $value): array;
}
