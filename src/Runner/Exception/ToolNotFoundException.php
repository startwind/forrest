<?php

namespace Startwind\Forrest\Runner\Exception;

class ToolNotFoundException extends \RuntimeException
{
    public function __construct(string $tool)
    {
        $message = "Unable to run the command. The tool \"$tool\" is not installed.";
        parent::__construct($message);
    }
}
