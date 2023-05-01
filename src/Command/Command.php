<?php

namespace Startwind\Forrest\Command;

class Command
{
    private string $prompt;
    private string $name;
    private string $description;

    /**
     * @param string $prompt
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description, string $prompt)
    {
        $this->prompt = $prompt;
        $this->name = $name;
        $this->description = $description;
    }

    public function getPrompt(array $values = []): string
    {
        $prompt = $this->prompt;

        foreach ($values as $key => $value) {
            $prompt = str_replace($key, $value, $prompt);
        }

        return $prompt;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function getParameters(): array
    {
        $prompt = $this->getPrompt();
        preg_match_all('^\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*^', $prompt, $matches);

        $parameters = [];

        foreach ($matches[0] as $match) {
            $parameters[] = $match;
        }

        return $parameters;
    }
}
