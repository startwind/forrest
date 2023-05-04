<?php

declare(strict_types=1);

namespace Tests\Startwind\Forrest\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Command\GistCommand;

final class GistCommandTest extends TestCase
{
    public function testGetPrompt(): void
    {
        $response = ['prompt' => 'some prompt'];

        $mock = new MockHandler([
            new Response(200, [], json_encode($response)),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $gistCommand = new GistCommand(
            'gistCommand',
            'gistCommand description',
            'https://some.raw.gist.url/forrest',
            $client
        );

        $prompt = $gistCommand->getPrompt();
        $this->assertJson($prompt);

        $request = $mock->getLastRequest();
        $this->assertEquals('https://some.raw.gist.url/forrest', (string)$request->getUri());
    }
}
