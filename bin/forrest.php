<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

const FORREST_VERSION = '##FORREST_VERSION##';

$application = new Application();

$application->setName('Startwind - Forrest: CLI Runner');
$application->setVersion(FORREST_VERSION);

# Command Commands
$application->add(new \Startwind\Forrest\CliCommand\Command\ListCommand());
$application->add(new \Startwind\Forrest\CliCommand\Command\ShowCommand());
$application->add(new \Startwind\Forrest\CliCommand\Command\RunCommand());

# Repository
$application->add(new \Startwind\Forrest\CliCommand\Repository\ListCommand());

$application->run();
