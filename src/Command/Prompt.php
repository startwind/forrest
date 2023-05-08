<?php

namespace Startwind\Forrest\Command;

class Prompt
{
    protected array $maskFilter = [
        'password',
        'secret',
    ];

    public function __construct(
        private readonly string $prompt,
        private readonly array $values = [],
    ) {
    }

    public function getPromptForExecute(): string
    {
        return $this->replaceParameters($this->prompt);
    }

    public function getPromptForOutput(): string
    {
        $prompt = $this->prompt;

        foreach ($this->values as $key => $value) {
            if ($this->isMasked($key)) {
                $prompt = str_replace(Command::PARAMETER_PREFIX . $key . Command::PARAMETER_POSTFIX, '********', $prompt);
            }
        }

        return $this->replaceParameters($prompt);
    }

    public function __toString(): string
    {
        return $this->getPromptForOutput();
    }

    protected function replaceParameters(string $prompt): string
    {
        foreach ($this->values as $key => $value) {
            $prompt = str_replace(Command::PARAMETER_PREFIX . $key . Command::PARAMETER_POSTFIX, (string)$value, $prompt);
        }

        return $prompt;
    }

    protected function isMasked(string $parameterName): bool
    {
        foreach ($this->maskFilter as $filter) {
            if (str_contains(strtolower($parameterName), $filter)) {
                return true;
            }
        }

        return false;
    }
}
