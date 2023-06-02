<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\Explode;

use Startwind\Forrest\Enrichment\EnrichFunction\ExplodeEnrichFunction;
use Startwind\Forrest\Logger\ForrestLogger;

class FunctionComposite implements ExplodeEnrichFunction
{
    /**
     * @var ExplodeEnrichFunction[] $functions
     */
    private static array $functions = [];

    public function __construct()
    {
        if (empty(self::$functions)) {
            self::$functions = [
                new DockerImagesStringFunction(),
                new DockerNamesStringFunction(),
            ];
        }
    }

    public function applyFunction(string $string): array
    {
        foreach (self::$functions as $function) {
            try {
                $result = $function->applyFunction($string);
            } catch (\Exception $exception) {
                ForrestLogger::error($exception->getMessage());
                continue;
            }

            if (is_array($result)) {
                return $result;
            }
        }

        return [];
    }
}
