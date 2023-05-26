<?php

namespace Tests\Startwind\Forrest\Repository;

use PHPUnit\Framework\MockObject\MockObject;
use Startwind\Forrest\Adapter\Adapter;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Repository\FileRepository;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\once;

class RepositoryTest extends TestCase
{
    private Adapter|MockObject|null $adapter = null;
    private ?FileRepository $subject = null;

    public function setUp(): void
    {
        $this->adapter = $this->createMock(Adapter::class);
        $this->subject = new FileRepository($this->adapter, 'name', 'description');

        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testGetter(): void
    {
        $this->assertEquals('name', $this->subject->getName());
        $this->assertEquals('description', $this->subject->getDescription());
        // $this->assertSame($this->adapter, $this->subject->getAdapter());

        $this->adapter->expects(once())->method('getCommands')->willReturn(['commands']);
        $this->assertEquals(['commands'], $this->subject->getCommands());

        // $this->adapter->expects(once())->method('isEditable')->willReturn(true);
        // $this->assertTrue($this->subject->isEditable());
    }
}
