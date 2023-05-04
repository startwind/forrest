<?php


declare(strict_types=1);

namespace Tests\Startwind\Forrest\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Command\Command;

final class CommandTest extends TestCase
{
    #[DataProvider('promptProvider')]
    public function testGetPrompt(array $values, string $prompt, string $expected): void
    {
        $command = new Command(
            'name',
            'description',
            $prompt
        );

        $this->assertEquals($expected, $command->getPrompt($values));
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
            [['a' => 'b'], '${a}', 'b'],
            [['a' => 'b' , 'b' => 'c'], '${a} is ${b}', 'b is c'],
            [['a' => 'b' , 'b' => 'c'], '${a} is ${c}', 'b is ${c}'],
            [['a' => 'b' , 'b' => 'c'], '${a}: ${b}, ${c}', 'b: c, ${c}'],
            [['a' => 'b' , 'b' => 'c'], '${a}: ${b}, ${c}', 'b: c, ${c}'],
        ];
    }
}
