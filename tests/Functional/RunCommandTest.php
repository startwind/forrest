<?php

namespace Tests\Startwind\Forrest\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\CliCommand\Command\RunCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends TestCase
{

    #[DataProvider('inputProvider')]
    public function testExecute(string $commandIdentifier, bool $isSuccessful, array $expectedOutputs, array $inputs = [])
    {
        $application = new Application();

        $application->add(new RunCommand());

        $command = $application->find(RunCommand::NAME);

        $commandTester = new CommandTester($command);

        if (count($inputs) > 0) {
            $commandTester->setInputs($inputs);
        }

        $commandTester->execute(['identifier' => $commandIdentifier]);
        $output = $commandTester->getDisplay();

        if ($isSuccessful) {
            $commandTester->assertCommandIsSuccessful();
        } else {
            $this->assertEquals(1, $commandTester->getStatusCode());
        }

        foreach ($expectedOutputs as $expectedOutput) {
            $this->assertStringContainsString($expectedOutput, $output);
        }
    }

    public static function inputProvider(): array
    {
        return [
            # command with date() function
            ['forrest-dev-tests:test:command:with-date', true, ['echo ' . date('Y-m-d'), 'Are you sure you want to run that command'], ['y']],
            # command with non-installed tool
            ['forrest-dev-tests:test:tool:not-exists', false, ['Are you sure you want to run that command', "The tool \"sls\" is not installed"], ['y']],
            # command with replaced password
            ['forrest-dev-tests:parameters:password', true, ['ls ${password}', 'ls ****', 'Are you sure you want to run that command'], [1234, 'y']],
        ];
    }
}
