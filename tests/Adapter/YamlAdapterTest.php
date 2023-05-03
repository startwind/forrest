<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Adapter\YamlAdapter;

final class YamlAdapterTest extends TestCase
{
    private $subject = null;
    
    public function setup(): void 
    {
        $this->subject = new YamlAdapter('username', 'prefix');
    }

    public function testGetType(): void
    {
        $this->assertEquals('yaml' , $this->subject->getType());
    }
}

