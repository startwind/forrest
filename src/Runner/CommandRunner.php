<?php

namespace Startwind\Forrest\Runner;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\History\HistoryHandler;
use Startwind\Forrest\Runner\Exception\ToolNotFoundException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class CommandRunner
{
    private array $prefixCommands = [
        'sudo'
    ];

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
    public function execute(string $prompt, bool $checkForExistence = true): CommandResult
    {
        if ($checkForExistence && !$this->toolExists($prompt, $tool)) {
            throw new ToolNotFoundException($tool);
        }

        $this->historyHandler->addEntry($prompt);

        exec($prompt . ' 2>&1', $execOutput, $resultCode);

        return new CommandResult($execOutput, $resultCode);
    }

    private function toolExists(string $prompt, &$command): bool
    {
        $parts = explode(' ', $prompt);

        $command = array_shift($parts);

        while (in_array($command, $this->prefixCommands) && !empty($parts)) {
            $command = array_shift($parts);
        }

        exec('which ' . $command, $output, $resultCode);

        return $resultCode == SymfonyCommand::SUCCESS;
    }
}
