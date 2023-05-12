<?php

namespace Startwind\Forrest\Adapter\Loader;

interface WritableLoader
{
    public function addCommand(string $commandName, array $command): void;
}
