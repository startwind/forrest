<?php

namespace Startwind\Forrest\Output;

use Startwind\Forrest\Command\Command;
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
            '<options=bold>Forrest</> - Unifying the Command Line <fg=green>' . FORREST_VERSION . '</>',
            // '         by Nils Langner and contributors.',
            '',
        ]);
    }

    /**
     * @param Command[] $commands
     */
    public static function renderCommands(
        OutputInterface $output,
        InputInterface  $input,
        QuestionHelper  $questionHelper,
        array           $commands,
        ?string         $repoIdentifier = null,
        int             $maxLength = -1,
        bool            $askForCommand = false,
        bool            $addAiOption = false
    ): bool|Command
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
            if ($a->getScore() > -1) {
                return $b->getScore() <=> $a->getScore();
            }
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

            $placeholder = '';

            if ($number < 100) {
                $placeholder = ' ';
            }

            if ($number < 11) {
                $placeholder = '  ';
            }

            $output->writeln($numberPrefix . '  <fg=green>' . $commandIdentifier . '</>' . $placeholder . $spaces . $command->getDescription());
        }

        if ($addAiOption) {
            $commandName = 'forrest:ai';
            $spaces = str_repeat(' ', $identifierMaxLength - strlen($commandName) + 4);
            //$output->writeln('');
            $dashes = str_repeat('-', $identifierMaxLength);
            $output->writeln('     ' . $dashes);
            // $output->writeln('');
            $output->writeln('  0  <fg=green>' . $commandName . '</>' . $spaces . "No matching command found. Please ask the Forrest AI.");
        }

        if ($askForCommand) {
            return self::askForCommand($output, $input, $questionHelper, $commands, $addAiOption);
        }

        return false;
    }

    /**
     * @param Command[] $commands
     */
    private static function askForCommand(OutputInterface $output, InputInterface $input, QuestionHelper $questionHelper, array $commands, bool $allowZero = false): bool|Command
    {
        $output->writeln('');

        if (count($commands) == 1 && !$allowZero) {
            $commandIdentifier = array_key_first($commands);
            $command = array_pop($commands);
            if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('  Do you want to run "' . $commandIdentifier . '" (y/n)? ', false))) {
                return false;
            }
        } else {
            $commandNumber = -1;
            if ($allowZero) {
                $minCommandNumber = 0;
            } else {
                $minCommandNumber = 1;
            }

            while ($commandNumber < $minCommandNumber || $commandNumber > count($commands)) {
                $commandNumber = (int)$questionHelper->ask($input, $output, new Question('  Which command do you want to run [' . $minCommandNumber . '-' . count($commands) . ']? '));
            }

            if ($commandNumber === 0) {
                return true;
            }

            $commandIdentifier = array_keys($commands)[$commandNumber - 1];
            $command = $commands[$commandIdentifier];
        }

        $command->setFullyQualifiedIdentifier($commandIdentifier);

        return $command;
    }
}
