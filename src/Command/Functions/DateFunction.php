<?php

namespace Startwind\Forrest\Command\Functions;

class DateFunction implements PromptFunction
{
    public function applyFunction($prompt): string
    {
        return $prompt;
    }
}
