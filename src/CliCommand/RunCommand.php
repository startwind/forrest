<?php

namespace Startwind\Forrest\CliCommand;

use Startwind\Forrest\Output\PromptHelper;
use Startwind\Forrest\Output\RunHelper;
use Startwind\Forrest\Runner\Exception\ToolNotFoundException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class RunCommand extends ForrestCommand
{
    protected function runCommand(string $commandIdentifier, array $userParameters): int
    {
        $repositoryIdentifier = $this->getRepositoryIdentifier($commandIdentifier);
        $questionHelper = $this->getHelper('question');

        $promptHelper = new PromptHelper($this->getInput(), $this->getOutput(), $questionHelper, $this->getRecentParameterMemory());

        $command = $this->getCommand($commandIdentifier);

        $prompt = $promptHelper->askForPrompt($repositoryIdentifier, $command, $userParameters);

        $promptHelper->showFinalPrompt($prompt);

        $runHelper = new RunHelper($this->getInput(), $this->getOutput(), $questionHelper, $this->getConfigHandler(), $this->getHistoryHandler());

        $force = !$this->getInput()->getOption('force') === false;

        if (!$runHelper->handleRunnable($command)) {
            return SymfonyCommand::SUCCESS;
        }

        if (!$runHelper->handleForceOption($force, $command, $repositoryIdentifier)) {
            return SymfonyCommand::FAILURE;
        }

        if (!$runHelper->confirmRun($force)) {
            return SymfonyCommand::FAILURE;
        }

        $this->getOutput()->writeln('');

        try {
            $runHelper->executeCommand($prompt);
        } catch (ToolNotFoundException $exception) {
            $this->renderErrorBox($exception->getMessage());
            return SymfonyCommand::FAILURE;
        }

        $this->getConfigHandler()->persistChecksum($command, $repositoryIdentifier);
        $this->getRecentParameterMemory()->dump();

        return SymfonyCommand::SUCCESS;
    }
}
