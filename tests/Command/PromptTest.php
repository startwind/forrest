<?php

namespace Tests\Startwind\Forrest\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Command\Prompt;

final class PromptTest extends TestCase
{
    #[DataProvider('promptProvider')]
    public function testGetPrompt(string $promptString, array $values, string $expected): void
    {
        $prompt = new Prompt($promptString, $values);

        $this->assertEquals($expected, $prompt->getPromptForExecute());
        $this->assertEquals($expected, $prompt->getPromptForOutput());
        $this->assertEquals($expected, (string)$prompt);
    }

    #[DataProvider('promptProviderWithSecrets')]
    public function testGetPromptWithSecrets(string $promptString, array $values, string $expected): void
    {
        $prompt = new Prompt($promptString, $values);

        $secretContent = current($values);

        $this->assertStringContainsString($secretContent, $prompt->getPromptForExecute());
        $this->assertEquals($expected, $prompt->getPromptForOutput());
        $this->assertEquals($expected, (string)$prompt);
    }

    public static function promptProvider(): array
    {
        return [
            'prompt with int parameter' => [
                'prompt ${param}',
                ['param' => 1],
                'prompt 1',
            ],
            'prompt with string parameter' => [
                'prompt ${param}',
                ['param' => '1'],
                'prompt 1',
            ],
            'prompt with missing parameter' => [
                'prompt ${param}',
                [],
                'prompt ${param}',
            ],
            'prompt with wrong parameter name' => [
                'prompt ${param}',
                ['param2' => 1],
                'prompt ${param}',
            ],
        ];

    }

    public static function promptProviderWithSecrets(): array
    {
        return [
            'prompt with password parameter' => [
                'prompt ${somePassword}',
                ['somePassword' => 'verySecret'],
                'prompt' => 'prompt ********'
            ],
            'prompt with secret parameter' => [
                'prompt ${someSecret}',
                ['someSecret' => 'verySecret'],
                'prompt' => 'prompt ********'
            ]
        ];
    }
}
