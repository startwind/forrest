<?php

namespace Startwind\Forrest\CliCommand;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Output\PromptHelper;
use Startwind\Forrest\Output\RunHelper;
use Startwind\Forrest\Repository\RepositoryCollection;
use Startwind\Forrest\Repository\StatusAwareRepository;
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

        if (!$runHelper->handleRunnable($command, $prompt->getFinalPrompt())) {
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
            $exitCode = $runHelper->executeCommand($this->getOutput(), $command, $prompt);
        } catch (ToolNotFoundException $exception) {
            $this->getRepositoryCollection()->pushStatus($command->getFullyQualifiedIdentifier(), StatusAwareRepository::STATUS_FAILURE);
            $this->renderErrorBox($exception->getMessage());
            return SymfonyCommand::FAILURE;
        }

        $this->getOutput()->writeln('');

        if ($exitCode == SymfonyCommand::SUCCESS) {
            $this->getOutput()->writeln('<info>Command ran successfully.');
        } else {
            $this->getOutput()->writeln('<error>' . $exitCode . ' Command did not run successfully.');
        }

        $this->getConfigHandler()->persistChecksum($command, $repositoryIdentifier);
        $this->getRecentParameterMemory()->dump();

        $this->getRepositoryCollection()->pushStatus($command->getFullyQualifiedIdentifier(), StatusAwareRepository::STATUS_SUCCESS);

        return SymfonyCommand::SUCCESS;
    }
}
