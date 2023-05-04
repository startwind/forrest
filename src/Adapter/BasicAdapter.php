<?php

namespace Startwind\Forrest\Adapter;

abstract class BasicAdapter implements Adapter
{
    const PARAMETER_PREFIX = '${';
    const PARAMETER_POSTFIX = '}';

    protected function extractParametersFromPrompt(string $prompt): array
    {
        preg_match_all('^\${[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*}^', $prompt, $matches);

        $parameters = [];

        foreach ($matches[0] as $match) {
            $parameters[] = str_replace(self::PARAMETER_PREFIX, '', str_replace(self::PARAMETER_POSTFIX, '', $match));
        }

        return $parameters;
    }
}
