<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction;

class FunctionComposite implements EnrichFunction
{
    public function applyFunction(string $string): string
    {
        /** @var EnrichFunction[] $functions */
        $functions = [
            new DateFunction(),
            new EnvFunction()
        ];

        foreach ($functions as $function) {
            $string = $function->applyFunction($string);
        }

        return $string;
    }
}
