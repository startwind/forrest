<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\CliCommand\RunCommand;
use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class SearchCommand extends RunCommand
{
    /**
     * Choose from a list of commands and execute one.
     */
    protected function runFromCommands(array $commands, $values = []): int
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $command = OutputHelper::renderCommands(
            $this->getOutput(),
            $this->getInput(),
            $questionHelper,
            $commands,
            null,
            -1,
            true
        );

        if ($command === false) {
            return SymfonyCommand::FAILURE;
        }

        $this->getOutput()->writeln('');

        return $this->runCommand($command, $values);
    }
}
