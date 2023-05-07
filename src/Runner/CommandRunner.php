<?php

namespace Startwind\Forrest\Runner;

class CommandRunner
{
    /**
     * Return a string array with all the commands. This is needed for multi line
     * commands.
     */
    public static function stringToMultilinePrompt(string $string): array
    {
        $commands = explode("\n", $string);

        if ($commands[count($commands) - 1] == '') {
            unset($commands[count($commands) - 1]);
        }

        return $commands;
    }
}
