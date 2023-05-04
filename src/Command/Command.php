<?php

namespace Startwind\Forrest\Command;

class Command
{
    const PARAMETER_PREFIX = '${';
    const PARAMETER_POSTFIX = '}';

    private string $prompt;
    private string $name;
    private string $description;

    private bool $isRunnable = true;

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

    /**
     * Return true if the prompt can be run via Forrest.
     */
    public function isRunnable(): bool
    {
        return $this->isRunnable;
    }

    /**
     * If this method is called Forrest will not run the command but only show it.
     */
    public function flagAsNotRunnable(): void
    {
        $this->isRunnable = false;
    }

    /**
     * Return the prompt. If the values are set the parameters will be set and the
     * prompt completed.
     */
    public function getPrompt(array $values = []): string
    {
        $prompt = $this->prompt;

        foreach ($values as $key => $value) {
            $prompt = str_replace(self::PARAMETER_PREFIX . $key . self::PARAMETER_POSTFIX, $value, $prompt);
        }

        return $prompt;
    }

    /**
     * Return the name of the command.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the description of the command.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Return the parameters that have to be inserted.
     */
    public function getParameters(): array
    {
        $prompt = $this->getPrompt();
        preg_match_all('^\${[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*}^', $prompt, $matches);

        $parameters = [];

        foreach ($matches[0] as $match) {
            $parameters[] = str_replace(self::PARAMETER_PREFIX, '', str_replace(self::PARAMETER_POSTFIX, '', $match));
        }

        return $parameters;
    }

    /**
     * Return the checksum of the command. This is used to check if a command
     * was changed.
     */
    public function getChecksum(): string
    {
        return md5($this->getPrompt());
    }
}
