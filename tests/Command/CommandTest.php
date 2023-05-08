<?php


declare(strict_types=1);

namespace Tests\Startwind\Forrest\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Prompt;

final class CommandTest extends TestCase
{
    #[DataProvider('promptProvider')]
    public function testGetPrompt(array $values, string $prompt, string $expected): void
    {
        $command = new Command(
            'name',
            'description',
            new Prompt($prompt, $values)
        );

        $prompt = $command->getPrompt();

        $this->assertEquals($expected, $prompt->getPromptForExecute());
        $this->assertEquals(md5($prompt->getPromptForExecute()), $command->getChecksum());
    }

    public function testGetter(): void
    {
        $command = new Command(
            'name',
            'description',
            new Prompt('prompt')
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
            [['a' => 'b'], '${a}', 'b'],
            [['a' => 'b' , 'b' => 'c'], '${a} is ${b}', 'b is c'],
            [['a' => 'b' , 'b' => 'c'], '${a} is ${c}', 'b is ${c}'],
            [['a' => 'b' , 'b' => 'c'], '${a}: ${b}, ${c}', 'b: c, ${c}'],
            [['a' => 'b' , 'b' => 'c'], '${a}: ${b}, ${c}', 'b: c, ${c}'],
        ];
    }
}
