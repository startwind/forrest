<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\Explode;

use Startwind\Forrest\Enrichment\EnrichFunction\ExplodeEnrichFunction;

class FunctionComposite implements ExplodeEnrichFunction
{
    public function applyFunction(string $string): string|array
    {
        /** @var ExplodeEnrichFunction[] $functions */
        $functions = [
            new DockerImagesStringFunction(),
        ];

        foreach ($functions as $function) {
            $result = $function->applyFunction($string);
            if (is_array($result)) {
                return $result;
            }
        }

        return $string;
    }
}
