<?php

namespace Startwind\Forrest\Command\Functions;

interface PromptFunction
{
    public function applyFunction($prompt): string;
}
