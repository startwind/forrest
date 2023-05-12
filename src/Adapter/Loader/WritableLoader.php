<?php

namespace Startwind\Forrest\Adapter\Loader;

interface WritableLoader
{
    /**
     * Write a configuration to a file or stream or whatever.
     */
    public function write(string $content);
}
