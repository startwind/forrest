<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\Repository;
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
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $maxLength = 0;

        $repositories = $this->getRepositoryCollection()->getRepositories();

        foreach ($repositories as $repoIdentifier => $repository) {
            try {
                foreach ($repository->getCommands() as $command) {
                    $maxLength = max($maxLength, strlen(Repository::createUniqueCommandName($repoIdentifier, $command)));
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

            OutputHelper::renderCommands($output, $repository->getCommands(), $repoIdentifier, $maxLength);
        }

        $output->writeln('');
    }
}
