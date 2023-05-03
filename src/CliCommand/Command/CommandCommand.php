<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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

    /**
     * Render the list output
     */
    protected function renderListCommand(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln("Forrest - Package manager for CLI scripts <fg=green>" . FORREST_VERSION . '</>');
        $output->writeln('');

        $this->enrichRepositories();

        $maxLength = 0;

        $repositories = $this->getRepositoryCollection()->getRepositories();

        foreach ($repositories as $repoIdentifier => $repository) {
            try {
                foreach ($repository->getCommands() as $command) {
                    $maxLength = max($maxLength, strlen($repoIdentifier . ':' . $command->getName()));
                }
            } catch (\Exception $exception) {
                unset($repositories[$repoIdentifier]);
                $this->writeWarning($output, [
                    'Unable to fetch commands from ' . $repoIdentifier . '. ' . $exception->getMessage()
                ]);
                $output->writeln('');
            }
        }

        $output->writeln('<fg=yellow>Usage:</>');
        $output->writeln('');
        $output->writeln('  forrest run [command]');
        $output->writeln('');

        foreach ($repositories as $repoIdentifier => $repository) {
            $output->writeln('');
            $output->writeln('<fg=yellow>' . $repository->getName() . '</> (' . $repoIdentifier . ')');
            $output->writeln('');

            $commands = $repository->getCommands();
            foreach ($commands as $command) {
                $commandIdentifier = $repoIdentifier . ':' . $command->getName();
                $spaces = str_repeat(' ', $maxLength - strlen($commandIdentifier) + 2);
                $output->writeln('  <fg=green>' . $commandIdentifier . '</>' . $spaces . $command->getDescription());
            }
        }

        $output->writeln('');
    }
}
