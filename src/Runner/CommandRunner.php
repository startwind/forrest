<?php

namespace Startwind\Forrest\Runner;

use Startwind\Forrest\History\HistoryHandler;
use Startwind\Forrest\Runner\Exception\ToolNotFoundException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CommandRunner
{
    private static array $prefixCommands = [
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
    public function execute(OutputInterface $output, string $prompt, bool $checkForExistence = true, $storeInHistory = true): int
    {
        if ($checkForExistence && !self::isToolInstalled($prompt, $tool)) {
            throw new ToolNotFoundException($tool);
        }

        if ($storeInHistory) {
            $this->historyHandler->addEntry($prompt);
        }

        $tool = self::extractToolFromPrompt($prompt);
        $arguments = trim(str_replace($tool, '', $prompt));

        $process = new Process([$tool, $arguments]);
        $process->run(function (string $pipe, string $outputString) use ($output) {
            if ($pipe == Process::OUT) {
                $output->write($outputString);
            } elseif ($pipe == Process::ERR) {
                $output->write("<error>" . $outputString . "</error>");
            } else {
                $output->write("<info>" . $outputString . "</info>");
            }
        });

        return $process->getExitCode();
    }

    /**
     * Return true if the tool is installed.
     */
    public static function isToolInstalled(string $prompt, &$command): bool
    {
        $command = self::extractToolFromPrompt($prompt);
        exec('which ' . $command, $output, $resultCode);
        return $resultCode == SymfonyCommand::SUCCESS;
    }

    /**
     * Get the tool name from a prompt.
     *
     * It also removes sudo and other "prefix" tools.
     */
    public static function extractToolFromPrompt(string $prompt): string
    {
        $parts = explode(' ', $prompt);

        $command = array_shift($parts);

        while (in_array($command, self::$prefixCommands) && !empty($parts)) {
            $command = array_shift($parts);
        }

        return $command;
    }
}
