<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\String;

use Startwind\Forrest\Enrichment\EnrichFunction\StringEnrichFunction;

class FunctionComposite implements StringEnrichFunction
{
    public function applyFunction(string $string): string
    {
        /** @var StringEnrichFunction[] $functions */
        $functions = [
            new DateStringFunction(),
            new EnvStringFunction()
        ];

        foreach ($functions as $function) {
            $string = $function->applyFunction($string);
        }

        return $string;
    }
}
