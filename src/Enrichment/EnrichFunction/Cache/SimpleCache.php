<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\Cache;

class SimpleCache
{
    private array $values = [];

    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->values[$key];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }
}
