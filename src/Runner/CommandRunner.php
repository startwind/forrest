<?php

namespace Startwind\Forrest\Runner;

use Startwind\Forrest\History\HistoryHandler;

class CommandRunner
{
    private HistoryHandler $historyHandler;

    public function __construct(HistoryHandler $historyHandler)
    {
        $this->historyHandler = $historyHandler;
    }

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

    /**
     * Run a single command line.
     */
    public function execute(string $prompt): CommandResult
    {
        $this->historyHandler->addEntry($prompt);

        exec($prompt . ' 2>&1', $execOutput, $resultCode);

        return new CommandResult($execOutput, $resultCode);
    }
}
