#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use SelfUpdate\SelfUpdateCommand;
use Symfony\Component\Console\Application;

if (!function_exists('get_debug_type')) {
    function get_debug_type($value): string
    {
        return \Symfony\Polyfill\Php80\Php80::get_debug_type($value);
    }
}

const FORREST_VERSION = '##FORREST_VERSION##';
const FORREST_NAME = 'Forrest';

$application = new Application();

$application->setName('Forrest - Package manager for CLI scripts');
$application->setVersion(FORREST_VERSION);

# AI
$application->add(new \Startwind\Forrest\CliCommand\Ai\AskCommand());
$application->add(new \Startwind\Forrest\CliCommand\Ai\ExplainCommand());

# Command Commands
$application->add(new \Startwind\Forrest\CliCommand\Command\ListCommand());
$application->add(new \Startwind\Forrest\CliCommand\Command\RunCommand());
$application->add(new \Startwind\Forrest\CliCommand\Command\ExplainCommand());
$application->add(new \Startwind\Forrest\CliCommand\Command\HistoryCommand());

# Repository
$application->add(new \Startwind\Forrest\CliCommand\Repository\ListCommand());
$application->add(new \Startwind\Forrest\CliCommand\Repository\CreateCommand());
$application->add(new \Startwind\Forrest\CliCommand\Repository\RegisterCommand());
$application->add(new \Startwind\Forrest\CliCommand\Repository\RemoveCommand());

# Repository Command
$application->add(new \Startwind\Forrest\CliCommand\Repository\Command\AddCommand());
$application->add(new \Startwind\Forrest\CliCommand\Repository\Command\RemoveCommand());
$application->add(new \Startwind\Forrest\CliCommand\Repository\Command\MoveAllCommand());


# Directory
$application->add(new \Startwind\Forrest\CliCommand\Directory\ListCommand());
$application->add(new \Startwind\Forrest\CliCommand\Directory\InstallCommand());
$application->add(new \Startwind\Forrest\CliCommand\Directory\ImportCommand());
$application->add(new \Startwind\Forrest\CliCommand\Directory\ExportCommand());
$application->add(new \Startwind\Forrest\CliCommand\Directory\RemoveCommand());

# Search
$application->add(new \Startwind\Forrest\CliCommand\Search\FileCommand());
$application->add(new \Startwind\Forrest\CliCommand\Search\PatternCommand());
$application->add(new \Startwind\Forrest\CliCommand\Search\ToolCommand());

# Forrest
$application->add(new \Startwind\Forrest\CliCommand\Forrest\HelpCommand());

# Others
if (!str_contains(FORREST_VERSION, '##FORREST_VERSION')) {
    $application->add(new SelfUpdateCommand(FORREST_NAME, FORREST_VERSION, "startwind/forrest"));
}

$application->setDefaultCommand(\Startwind\Forrest\CliCommand\Forrest\HelpCommand::COMMAND_NAME);

$application->run();
