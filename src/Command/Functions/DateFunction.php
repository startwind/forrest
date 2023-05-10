<?php

namespace Startwind\Forrest\Command\Functions;

class DateFunction implements PromptFunction
{
    protected const FUNCTION_LIMITER_START = '${';
    protected const FUNCTION_LIMITER_END = '}';

    public function applyFunction($prompt): string
    {
        $pattern = '#' . preg_quote(self::FUNCTION_LIMITER_START) . 'date\((.*?)\)' . preg_quote(self::FUNCTION_LIMITER_END) . '#';
        preg_match_all($pattern, $prompt, $matches);
        if (count($matches) > 0) {
            foreach ($matches[1] as $dateFormat) {
                $prompt = str_replace(self::FUNCTION_LIMITER_START . 'date(' . $dateFormat . ')}', date($dateFormat), $prompt);
            }
        }
        return $prompt;
    }
}
