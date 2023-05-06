<?php

namespace Tests\Startwind\Forrest\CliCommand\Command;

use PHPUnit\Framework\TestCase;
use Startwind\Forrest\CliCommand\Command\ListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

define('FORREST_VERSION', '0.0.0-phpunit');

class ListCommandTest extends TestCase
{
    private ?CommandTester $commandTester = null;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new ListCommand());
        $command = $application->find('commands:list');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([]);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('forrest-linux', $output);
    }
}
