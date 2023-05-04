<?php

namespace Tests\Startwind\Forrest\History;

use Startwind\Forrest\History\HistoryHandler;
use PHPUnit\Framework\TestCase;

class HistoryHandlerTest extends TestCase
{
    private ?string $tmpFilename = null;

    public function setUp(): void
    {
        $this->tmpFilename = tempnam('/tmp', 'forrest');
        parent::setUp();
    }

    public function testGetEntries(): void
    {
        $history = new HistoryHandler($this->tmpFilename);

        $expectedEntries = [
            'some',
            'command',
        ];

        foreach ($expectedEntries as $entry) {
            $history->addEntry($entry);
        }

        $fileContent = file_get_contents($this->tmpFilename);

        foreach ($expectedEntries as $expectedEntry) {
            $this->assertContains($expectedEntry . "\n", $history->getEntries());
            $this->assertStringContainsString($expectedEntry, $fileContent);
        }
    }

    public function tearDown(): void
    {
        unlink($this->tmpFilename);
        parent::tearDown();
    }
}
