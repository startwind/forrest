<?php

declare(strict_types=1);

namespace Tests\Startwind\Forrest\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\Parameter;
use Startwind\Forrest\Command\Parameters\ParameterValue;
use Startwind\Forrest\Command\Prompt;

final class CommandTest extends TestCase
{
    /**
     * @dataProvider promptProvider
     */
    public function testGetPrompt(array $values, string $prompt, string $expected): void
    {
        $command = new Command(
            'name',
            'description',
            $prompt
        );

        $finalPrompt = new Prompt($command->getPrompt(), $values);

        $this->assertEquals($expected, $finalPrompt->getFinalPrompt());
        $this->assertEquals(md5($prompt), $command->getChecksum());
    }

    public function testGetter(): void
    {
        $command = new Command(
            'name',
            'description',
            'prompt'
        );

        $this->assertEquals('name', $command->getName());
        $this->assertEquals('description', $command->getDescription());
    }

    public static function promptProvider(): array
    {
        return [
            [[], '', ''],
            [[], 'a', 'a'],
            [[], '${a}', '${a}'],
            [[new ParameterValue('a', 'b', Parameter::TYPE)], '${a}', 'b'],
            [[new ParameterValue('a', 'b', Parameter::TYPE), new ParameterValue('b', 'c', Parameter::TYPE)], '${a} is ${b}', 'b is c'],
            [[new ParameterValue('a', 'b', Parameter::TYPE), new ParameterValue('b', 'c', Parameter::TYPE)], '${a} is ${c}', 'b is ${c}'],
            [[new ParameterValue('a', 'b', Parameter::TYPE), new ParameterValue('b', 'c', Parameter::TYPE)], '${a}: ${b}, ${c}', 'b: c, ${c}']
        ];
    }
}
