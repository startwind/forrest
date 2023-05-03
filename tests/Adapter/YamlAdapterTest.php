<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Adapter\YamlAdapter;

final class YamlAdapterTest extends TestCase
{
    private $subject = null;
    
    public function setup(): void 
    {
        $this->subject = new YamlAdapter('yamlfile.yaml');
    }

    public function testGetType(): void
    {
        $this->assertEquals('yaml' , $this->subject->getType());
    }

    /**
     * @dataProvider yamlConfigProvider
     */
    public function testConfigArray(array $config): void 
    {
        $result = YamlAdapter::fromConfigArray($config);
        $this->assertInstanceOf(YamlAdapter::class, $result);
    }

    static public function yamlConfigProvider(): array
    {
        return [
            [['file' => 'yaml.file']],
            [['file' => 'yaml.file', 'somethingElse' => 'doesnt matter']],
        ];
    }
}

