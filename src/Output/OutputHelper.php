<?php

namespace Startwind\Forrest\Output;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Repository\Repository;
use Symfony\Component\Console\Output\OutputInterface;

class OutputHelper
{
    /**
     * Render the header including version
     */
    public static function renderHeader(OutputInterface $output): void
    {
        $output->writeln([
            '',
            'Forrest - Package manager for CLI scripts <fg=green>' . FORREST_VERSION . '</>',
            '',
        ]);
    }

    /**
     * Render a collection of commands.
     */
    public static function renderCommands(OutputInterface $output, array $commands, string $repoIdentifier = null, int $maxLength = -1): void
    {
        if ($maxLength == -1) {
            foreach ($commands as $commandId => $command) {
                if ($repoIdentifier) {
                    $commandIdentifier = Repository::createUniqueCommandName($repoIdentifier, $command);
                } else {
                    $commandIdentifier = $commandId;
                }
                $maxLength = max($maxLength, strlen($commandIdentifier));
            }
        }

        uasort($commands, function (Command $a, Command $b) {
            return $a->getName() <=> $b->getName();
        });

        foreach ($commands as $commandId => $command) {
            if ($repoIdentifier) {
                $commandIdentifier = Repository::createUniqueCommandName($repoIdentifier, $command);
            } else {
                $commandIdentifier = $commandId;
            }
            $spaces = str_repeat(' ', $maxLength - strlen($commandIdentifier) + 2);
            $output->writeln('  <fg=green>' . $commandIdentifier . '</>' . $spaces . $command->getDescription());
        }
    }

    public static function renderCommandWithExplanation(OutputInterface $output, Command $command): void
    {
        $output->writeln('');
        $output->writeln('');

        $prompt = $command->getPrompt();
        $parameters = $command->getParameters();

        $positions = [];

        foreach ($parameters as $parameterName => $parameter) {
            $positions[] = strpos($prompt, $parameterName);
        }

        for ($i)

        foreach ($positions as $position) {
            var_dump($position);
        }
        echo 'Hier';

        $output->writeln('');
        $output->writeln('');
    }
}
