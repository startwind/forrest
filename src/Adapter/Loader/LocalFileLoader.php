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
        return file_get_contents($this->filename);
    }

    /**
     * @inheritDoc
     */
    public static function fromConfigArray(array $config): Loader
    {
        return new self($config['file']);
    }

    public function write(string $content)
    {
        file_put_contents($this->filename, $content);
    }
}
