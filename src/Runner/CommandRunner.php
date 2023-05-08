<?php

namespace Startwind\Forrest\Runner;

use Startwind\Forrest\Command\Prompt;
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
     *
     * @return Prompt[]
     */
    public static function promptToMultilinePrompt(Prompt $prompt): array
    {
        $commands = explode("\n", $prompt->getPromptForExecute());

        if ($commands[count($commands) - 1] == '') {
            unset($commands[count($commands) - 1]);
        }

        $prompts = [];

        foreach ($commands as $command) {
            $prompts[] = new Prompt($command, $prompt->getValues());
        }

        return $prompts;
    }

    /**
     * Run a single command line.
     */
    public function execute(Prompt $prompt, bool $checkForExistence = true): CommandResult
    {
        if ($checkForExistence && !$this->toolInstalled($prompt->getPromptForExecute(), $tool)) {
            throw new ToolNotFoundException($tool);
        }

        $this->historyHandler->addEntry($prompt->getPromptForOutput());

        exec($prompt->getPromptForExecute() . ' 2>&1', $execOutput, $resultCode);

        return new CommandResult($execOutput, $resultCode);
    }

    /**
     * Return true if the tool is installed.
     */
    private function toolInstalled(string $prompt, &$command): bool
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
