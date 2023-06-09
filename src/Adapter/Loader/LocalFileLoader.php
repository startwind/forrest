<?php

namespace Startwind\Forrest\Adapter\Loader;

class LocalFileLoader implements Loader, WritableLoader
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @inheritDoc
     */
    public function load(): string
    {
        if (!file_exists($this->filename)) {
            throw new \RuntimeException('The mandatory file ("' . $this->filename . '") does not exist.');
        }
        return file_get_contents($this->filename);
    }

    /**
     * @inheritDoc
     */
    public static function fromConfigArray(array $config): Loader
    {
        return new self($config['file']);
    }

    public function assertHealth(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function write(string $content)
    {
        file_put_contents($this->filename, $content);
    }
}
