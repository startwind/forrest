<?php

namespace Startwind\Forrest\CliCommand;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Output\PromptHelper;
use Startwind\Forrest\Output\RunHelper;
use Startwind\Forrest\Repository\RepositoryCollection;
use Startwind\Forrest\Runner\Exception\ToolNotFoundException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;

abstract class RunCommand extends ForrestCommand
{
    /**
     * Run the actual command and ask the user for the details.
     */
    protected function runCommand(Command|string $command, array $userParameters = []): int
    {
        if (is_string($command)) {
            $commandIdentifier = $command;
            $command = $this->getCommand($command);
        } else {
            $commandIdentifier = $command->getFullyQualifiedIdentifier();
        }

        $repositoryIdentifier = RepositoryCollection::getRepositoryIdentifier($commandIdentifier);

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $promptHelper = new PromptHelper($this->getInput(), $this->getOutput(), $questionHelper, $this->getRecentParameterMemory());

        $prompt = $promptHelper->askForPrompt($command, $userParameters);

        $promptHelper->showFinalPrompt($prompt);

        $runHelper = new RunHelper($this->getInput(), $this->getOutput(), $questionHelper, $this->getConfigHandler(), $this->getHistoryHandler());

        $force = $this->getInput()->getOption('force');

        var_dump($force);

        if (!$runHelper->handleRunnable($command, $prompt->getFinalPrompt())) {
            var_dump(0);
            return SymfonyCommand::SUCCESS;
        }

        if (!$runHelper->handleForceOption($force, $command, $repositoryIdentifier)) {
            var_dump(1);
            return SymfonyCommand::FAILURE;
        }

        if (!$runHelper->confirmRun($force)) {
            var_dump(2);
            return SymfonyCommand::FAILURE;
        }

        $this->getOutput()->writeln('');

        try {
            $runHelper->executeCommand($command, $prompt);
        } catch (ToolNotFoundException $exception) {
            $this->renderErrorBox($exception->getMessage());
            return SymfonyCommand::FAILURE;
        }

        $this->getConfigHandler()->persistChecksum($command, $repositoryIdentifier);
        $this->getRecentParameterMemory()->dump();

        return SymfonyCommand::SUCCESS;
    }
}
