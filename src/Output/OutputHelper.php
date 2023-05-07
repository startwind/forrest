<?php

namespace Startwind\Forrest\Output;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Repository\Repository;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class OutputHelper
{
    public static function renderTable(OutputInterface $output, array $headers, array $rows): void
    {
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
    }

    public static function renderHeader(OutputInterface $output): void
    {
        $output->writeln([
            '',
            'Forrest - Package manager for CLI scripts <fg=green>' . FORREST_VERSION . '</>',
            '',
        ]);
    }

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
}
