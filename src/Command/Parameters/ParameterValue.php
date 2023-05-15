<?php

namespace Startwind\Forrest\Command\Parameters;

class ParameterValue
{
    private string $key;
    private string $value;
    private string $type;

    /**
     * @param string $key
     * @param string $value
     * @param string $type
     */
    public function __construct(string $key, string $value, string $type)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
