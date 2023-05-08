<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Prompt;
use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\Repository;
use Startwind\Forrest\Runner\CommandRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandCommand extends ForrestCommand
{
    /**
     * @var string[]
     */
    protected array $runWarning = [
        "Be careful. Please only run command that you understand. We only have limited control",
        "of repositories that are not owned by this project.",
    ];

    protected function showCommandInformation(OutputInterface $output, Command $command): void
    {
        $this->renderWarningBox($this->runWarning);

        $prompt = new Prompt($command->getPrompt());

        $commands = CommandRunner::promptToMultilinePrompt($prompt);

        $plural = (count($commands) > 1) ? 's' : '';

        $output->writeln('  Command' . $plural . ' to be run:');
        $output->writeln('');
        $this->renderInfoBox((string)$prompt);
        $output->writeln('');

    }

    /**
     * Render the list output
     */
    protected function renderListCommand(): void
    {
        $output = $this->getOutput();

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
                $this->renderErrorBox([
                    'Unable to fetch commands from ' . $repoIdentifier . '. ' . $exception->getMessage(),
                ]);
                $output->writeln('');
            }
        }

        $output->writeln([
            '<fg=yellow>Usage:</>',
            '',
            '  forrest run [command]',
            '',
        ]);

        foreach ($repositories as $repoIdentifier => $repository) {
            if ($repository->isSpecial()) {
                $this->renderWarningBox($repository->getName() . ' (' . $repoIdentifier . ')');
                $output->writeln(['  ' . $repository->getDescription(), '']);

            } else {
                $output->writeln([
                    '',
                    '<fg=yellow>' . $repository->getName() . '</> (' . $repoIdentifier . ')',
                    '',
                ]);
            }

            OutputHelper::renderCommands($output, $repository->getCommands(), $repoIdentifier, $maxLength);
        }

        $output->writeln('');
    }
}
