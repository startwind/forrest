<?php

declare(strict_types=1);

namespace Tests\Startwind\Forrest\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Adapter\GistAdapter;
use Startwind\Forrest\Command\GistCommand;

final class GistAdapterTest extends TestCase
{
    private ?GistAdapter $subject = null;

    public function setup(): void
    {
        $prefix = 'prefix';

        $this->subject = new GistAdapter('username', $prefix);

        $gistResponse = [
            [
                'description' => $prefix . 'some description',
                'files' => [
                    ['filename' => 'gist1', 'raw_url' => 'raw1'],
                    ['filename' => 'gist1', 'raw_url' => 'raw2'],
                    ['filename' => 'gist1', 'raw_url' => 'raw3'],
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($gistResponse)),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->subject->setClient($client);
    }

    public function testGetType(): void
    {
        $this->assertEquals('gist', $this->subject->getType());
    }

    public function testGetCommands(): void
    {
        $commands = $this->subject->getCommands();

        $this->assertCount(3, $commands);

        foreach ($commands as $command) {
            $this->assertInstanceOf(GistCommand::class, $command);
            $this->assertStringStartsWith('gist', $command->getName());
            $this->assertStringContainsString('description', $command->getDescription());
            $this->assertStringNotContainsString('prefix', $command->getDescription());
        }
    }
}

