<?php

namespace Startwind\Forrest\CliCommand\Forrest;

use Startwind\Forrest\CliCommand\ForrestCommand;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand extends ForrestCommand
{
    public const COMMAND_NAME = 'forrest:help';

    protected static $defaultName = self::COMMAND_NAME;
    protected static $defaultDescription = 'List all repositories in the official Forrest directory.';

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        \Startwind\Forrest\Output\OutputHelper::renderHeader($output);

        $output->writeln('');

        $output->writeln([
            '<fg=yellow>Usage:</>',
            '',
            '  forrest run [command | file | pattern]',
            '',
            '<fg=yellow>Common Forrest Commands:</>',
            '',
            '  <fg=green>forrest run [command]</>  Run a command by name. This is used when the user already knows what to do.',
            '                         To show a list of all locally stored commands (these are only a small set ',
            '                         of the overall available commands) run forrest commands:list.',
            '',
            '                         <fg=yellow>Example:</> forrest run forrest:linux:files:find:name',
            '',
            '  <fg=green>forrest run [file]</>     There are a lot of commands that are connected to a special file type.',
            '                         This Forrest command will take an existing file or directory as argument and ',
            '                         will then return all commands that are connected to this file type.',
            '',
            '                         <fg=yellow>Example:</> forrest run wordpress.zip',
            '',
            '  <fg=green>forrest run [tool]</>     If only a single word is used as an argument Forrest assumes that it is the name',
            '                         of a tool. It will return all commands that are found for this command line tool.',
            '',
            '                         <fg=yellow>Example:</> forrest run symfony',
            '',
            '  <fg=green>forrest run [pattern]</>  When the run command is used with a pattern as an argument it is used as',
            '                         full text search in the background. It will return all commands that have the  ',
            '                         given pattern in their name or description.',
            '',
            '                         <fg=yellow>Example:</> forrest run install mysql-cli',
            '',
            '',
            '<fg=yellow>Additional Forrest Commands:</>',
            '',
            '  <fg=green>forrest list</>           Show all available Forrest commands (many of them are not listed here).',
            '  <fg=green>forrest commands:list</>  Show all custom commands (might be empty in the beginning).',
            '  <fg=green>forrest history</>        Show the recent commands that were run by forrest.',
            '  <fg=green>forrest tool [tool]</>    Show all commands that are connected to the given tool.',
            '',
        ]);

        // $output->writeln('  <fg=green>' . $commandIdentifier . '</>' . $spaces . $command->getDescription());

        return SymfonyCommand::SUCCESS;
    }
}
