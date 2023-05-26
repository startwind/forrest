<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\FileRepository;
use Startwind\Forrest\Repository\ListAware;
use Startwind\Forrest\Repository\RepositoryCollection;

class CommandCommand extends \Startwind\Forrest\CliCommand\RunCommand
{
    /**
     * Render the list output
     */
    protected function renderListCommand(string $repository = ''): void
    {
        $output = $this->getOutput();

        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $maxLength = 0;

        if ($repository) {
            $repositories = [$repository => $this->getRepositoryCollection()->getRepository($repository)];
        } else {
            $repositories = $this->getRepositoryCollection()->getRepositories();
        }

        foreach ($repositories as $repoIdentifier => $repository) {
            if ($repository instanceof ListAware) {
                try {
                    foreach ($repository->getCommands() as $command) {
                        $maxLength = max($maxLength, strlen(RepositoryCollection::createUniqueCommandName($repoIdentifier, $command)));
                    }
                } catch (\Exception $exception) {
                    unset($repositories[$repoIdentifier]);
                    $this->renderErrorBox([
                        'Unable to fetch commands from ' . $repoIdentifier . '. ' . $exception->getMessage(),
                    ]);
                    $output->writeln('');
                }
            }
        }

        $output->writeln([
            '<fg=yellow>Usage:</>',
            '',
            '  forrest run [command]',
            '',
        ]);

        foreach ($repositories as $repoIdentifier => $repository) {
            if (!$repository->hasCommands()) {
                continue;
            }
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

            /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');

            OutputHelper::renderCommands($output, $this->getInput(), $questionHelper, $repository->getCommands(), $repoIdentifier, $maxLength);
        }

        $output->writeln('');
    }
}
