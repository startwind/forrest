<?php


declare(strict_types=1);

namespace Tests\Startwind\Forrest\Adapter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Startwind\Forrest\Adapter\Loader\LocalFileLoader;
use Startwind\Forrest\Adapter\YamlAdapter;

final class YamlAdapterTest extends TestCase
{
    private ?YamlAdapter $subject = null;

    public function setup(): void
    {
        $this->subject = new YamlAdapter(new LocalFileLoader('file.yaml'));
    }

    public function testGetType(): void
    {
        $this->assertEquals('yaml', $this->subject->getType());
    }

    #[DataProvider('yamlConfigProvider')]
    public function testConfigArray(array $config): void
    {
        $result = YamlAdapter::fromConfigArray($config);
        $this->assertInstanceOf(YamlAdapter::class, $result);
    }

    public static function yamlConfigProvider(): array
    {
        return [
            [['file' => 'yaml.file']],
            [['file' => 'yaml.file', 'somethingElse' => 'doesnt matter']],
        ];
    }
}
