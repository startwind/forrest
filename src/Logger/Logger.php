<?php

namespace Startwind\Forrest\Logger;

interface Logger
{
    public function error($message): void;

    public function warn($message): void;

    public function info($message): void;
}
