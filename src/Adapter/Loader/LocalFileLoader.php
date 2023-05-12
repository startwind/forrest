<?php

namespace Startwind\Forrest\Adapter\Loader;

class LocalFileLoader implements Loader
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
        return self($config['file']);
    }

    /**
     * @inheritDoc
     */
    public function isWriteable(): bool
    {
        return true;
    }
}
