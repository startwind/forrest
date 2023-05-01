<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class CommandCommand extends ForrestCommand
{
    protected array $runWarning = [
        "Be careful. Please only run command that you understand. We only have limited control",
        "of repositories that are not owned by this project."
    ];

    protected function showCommandInformation(OutputInterface $output, Command $command): void
    {
        $commands = explode("\n", $command->getPrompt());

        $this->writeWarning($output, $this->runWarning);

        $output->writeln('');
        $output->writeln('  Command to be run:');
        $this->writeInfo($output, $commands);
        $output->writeln('');

    }
}
