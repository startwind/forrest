<?php

namespace Startwind\Forrest\Command\Functions;

class DateFunction implements PromptFunction
{
    public function applyFunction($prompt): string
    {
        return str_replace('${date(Y-m-d)}', date('Y-m-d'), $prompt);
    }
}
