<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Adapter\GistAdapter;

final class GistAdapterTest extends TestCase
{
    private $subject = null;
    
    public function setup(): void 
    {
        $this->subject = new GistAdapter('username', 'prefix');

        // @TODO: create a mock client and set mockClient with
        //$this->subject->setClient($mockClient);
    }

    public function testGetType(): void
    {
        $this->assertEquals('gist' , $this->subject->getType());
    }
}

