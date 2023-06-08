<?php

namespace Startwind\Forrest\Output;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Prompt;
use Startwind\Forrest\Config\ConfigFileHandler;
use Startwind\Forrest\History\HistoryHandler;
use Startwind\Forrest\Runner\CommandRunner;
use Startwind\Forrest\Util\OSHelper;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RunHelper
{
    private InputInterface $input;

    private OutputInterface $output;

    private ConfigFileHandler $configHandler;

    private QuestionHelper $questionHelper;

    private HistoryHandler $historyHandler;

    public function __construct(
        InputInterface    $input,
        OutputInterface   $output,
        QuestionHelper    $questionHelper,
        ConfigFileHandler $configHandler,
        HistoryHandler    $historyHandler
    )
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->configHandler = $configHandler;
        $this->historyHandler = $historyHandler;
    }

    public function handleForceOption(bool $force, Command $command, string $repositoryIdentifier): bool
    {
        if (!$force) {
            return true;
        }

        $hasChanged = $this->configHandler->hasChecksumChanged($command, $repositoryIdentifier);

        if ($hasChanged) {
            return $this->questionHelper->ask($this->input, $this->output, new ConfirmationQuestion('  The signature of the command has changed since you last run it. Do you confirm to still run it? [y/n] ', false));
        } else {
            return true;
        }
    }

    public function handleRunnable(Command $command, string $finalPrompt): bool
    {
        if (!$command->isRunnable()) {
            $copied = OSHelper::copyToClipboard($finalPrompt);

            if ($copied) {
                $clipboardText = " It was copied to your clipboard.";
            } else {
                $clipboardText = "";
            }

            OutputHelper::writeWarningBox($this->output, [
                'This command was marked as not runnable by Forrest.' . $clipboardText
            ]);

            return false;
        } else {
            return true;
        }
    }

    public function confirmRun(bool $force): bool
    {
        if (!$force) {
            return $this->questionHelper->ask($this->input, $this->output, new ConfirmationQuestion('  Are you sure you want to run that command? [y/n] ', false));
        }

        return true;
    }

    /**
     * Run every single command in the executable command.
     */
    public function executeCommand(OutputInterface $output, Command $actualCommand, Prompt $prompt): void
    {
        $commands = CommandRunner::stringToMultilinePrompt($prompt->getFinalPrompt());

        $commandRunner = new CommandRunner($this->historyHandler);

        foreach ($commands as $command) {
            $exitCode = $commandRunner->execute(
                $output,
                $command,
                true,
                $actualCommand->isAllowedInHistory()
            );
        }
    }
}
