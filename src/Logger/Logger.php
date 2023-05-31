<?php

namespace Startwind\Forrest\Logger;

interface Logger
{
    /**
     * Log an error.
     */
    public function error($message): void;

    /**
     * Log a warning.
     */
    public function warn($message): void;

    /**
     * Log an information.
     */
    public function info($message): void;
}
