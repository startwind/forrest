<?php

namespace Startwind\Forrest\Adapter;

abstract class BasicAdapter implements Adapter
{
    public const PARAMETER_PREFIX = '${';
    public const PARAMETER_POSTFIX = '}';

    /**
     * @return string[]
     */
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
