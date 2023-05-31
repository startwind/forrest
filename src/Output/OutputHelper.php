<?php

namespace Startwind\Forrest\Output;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Repository\FileRepository;
use Startwind\Forrest\Repository\RepositoryCollection;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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
            '<options=bold>Forrest</>: the command line helper <fg=green>' . FORREST_VERSION . '</> by Nils Langner and contributors.',
            '',
        ]);
    }

    /**
     * @param Command[] $commands
     */
    public static function renderCommands(OutputInterface $output, InputInterface $input, QuestionHelper $questionHelper, array $commands, ?string $repoIdentifier = null, int $maxLength = -1, $askForCommand = false): bool|Command
    {
        $identifierMaxLength = $maxLength;

        foreach ($commands as $commandId => $command) {
            if ($repoIdentifier) {
                $commandIdentifier = RepositoryCollection::createUniqueCommandName($repoIdentifier, $command);
            } else {
                $commandIdentifier = $commandId;
            }
            $command->setFullyQualifiedIdentifier($commandIdentifier);
            if ($maxLength == -1) {
                $identifierMaxLength = max($identifierMaxLength, strlen($commandIdentifier));
            }
        }

        uasort($commands, function (Command $a, Command $b) {
            return $a->getFullyQualifiedIdentifier() <=> $b->getFullyQualifiedIdentifier();
        });

        $number = 1;
        $numberPrefix = '';

        foreach ($commands as $commandId => $command) {
            if ($repoIdentifier) {
                $commandIdentifier = RepositoryCollection::createUniqueCommandName($repoIdentifier, $command);
            } else {
                $commandIdentifier = $commandId;
            }

            $spaces = str_repeat(' ', $identifierMaxLength - strlen($commandIdentifier) + 2);

            if ($askForCommand) {
                $numberPrefix = '  ' . $number;
                $number++;
            }
            $output->writeln($numberPrefix . '  <fg=green>' . $commandIdentifier . '</>' . $spaces . $command->getDescription());
        }

        if ($askForCommand) {
            return self::askForCommand($output, $input, $questionHelper, $commands);
        }

        return false;
    }

    /**
     * @param Command[] $commands
     */
    private static function askForCommand(OutputInterface $output, InputInterface $input, QuestionHelper $questionHelper, array $commands): bool|Command
    {
        $output->writeln('');

        if (count($commands) == 1) {
            $commandIdentifier = array_key_first($commands);
            $command = array_pop($commands);
            if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('  Do you want to run "' . $commandIdentifier . '" (y/n)? ', false))) {
                return false;
            }
        } else {
            $commandNumber = 0;
            while ($commandNumber < 1 || $commandNumber > count($commands)) {
                $commandNumber = (int)$questionHelper->ask($input, $output, new Question('  Which command do you want to run [1-' . count($commands) . ']? '));
            }

            $commandIdentifier = array_keys($commands)[$commandNumber - 1];
            $command = $commands[$commandIdentifier];
        }


        $command->setFullyQualifiedIdentifier($commandIdentifier);

        return $command;
    }
}
