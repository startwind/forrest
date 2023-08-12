<?php

namespace Tests\Startwind\Forrest\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\CliCommand\Command\RunCommand;
use Startwind\Forrest\CliCommand\Repository\RegisterCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends TestCase
{
    public function setUp(): void
    {
        $application = new Application();
        $application->add(new RegisterCommand());
        $command = $application->find(RegisterCommand::NAME);
        $commandTester = new CommandTester($command);
        $commandTester->execute(['repositoryFileName' => __DIR__ . '/../commands/tests.yml']);
    }

    /**
     * @dataProvider inputProvider
     */
    public function testExecute(string $commandIdentifier, bool $isSuccessful, array $expectedOutputs, array $inputs = [])
    {
        $application = new Application();

        $application->add(new RunCommand());

        $command = $application->find(RunCommand::NAME);

        $commandTester = new CommandTester($command);

        if (count($inputs) > 0) {
            $commandTester->setInputs($inputs);
        }

        $commandTester->execute(['argument' => [$commandIdentifier]]);
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
            # command with kay-value enum
            ['forrest-dev-tests:parameters:enum:with-key', true, ['echo ${enum}', '[0] eins', 'one'], [0, 'y']],
            # command with parameter validation (constraint) - success
            ['forrest-dev-tests:constraints:identifier', true, ['echo ${identifier}', 'Select value for identifier'], ['a1b2c3', 'y']],
            # command with parameter validation (constraint) - failure
            ['forrest-dev-tests:constraints:identifier', false, ['echo ${identifier}', 'Select value for identifier'], ['a1 b2c3', 'y']],
            # works only locally ['forrest-dev-tests:parameters:enum:with-explode', true, ['[0]'], [0, 'y']],

            # command with custom enum
            ['forrest-dev-tests:parameter:enum:custom', true, ['echo "nils langner"', 'echo "${enum}"', 'Select value for enum'], [0, 'nils langner', 'y']],

            # command with pre- and suffix
            ['forrest-dev-tests:parameter:prefix-suffix', true, ['prefix 123 suffix', 'echo "${parameter}"'], ['123', 'y']],

        ];
    }
}
