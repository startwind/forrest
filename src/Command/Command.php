<?php

namespace Startwind\Forrest\Command;

use Startwind\Forrest\Command\Parameters\Parameter;
use Startwind\Forrest\Command\Parameters\PasswordParameter;

class Command implements \JsonSerializable
{
    private bool $isRunnable = true;

    private string $fullyQualifiedIdentifier = '';

    private string $outputFormat = '';

    private bool $allowedInHistory = true;

    private string $explanation = "";

    private float $score = -1;

    public function setOutputFormat(string $output): void
    {
        $this->outputFormat = $output;
    }

    /**
     * @var Parameter[]
     */
    private array $parameters = [];

    private array $plainCommandArray = [];

    public function __construct(private string $name, private readonly string $description, private readonly string $prompt, string $explanation = "")
    {
        $this->explanation = $explanation;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @param float $score
     */
    public function setScore(float $score): void
    {
        $this->score = $score;
    }

    /**
     * @param array $plainCommandArray
     */
    public function setPlainCommandArray(array $plainCommandArray): void
    {
        $this->plainCommandArray = $plainCommandArray;
    }

    /**
     * Return true if the prompt can be run via Forrest.
     */
    public function isRunnable(): bool
    {
        return $this->isRunnable;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * If this method is called Forrest will not run the command but only show it.
     */
    public function flagAsNotRunnable(): void
    {
        $this->isRunnable = false;
    }

    /**
     * Return the prompt.
     */
    public function getPrompt(): string
    {
        return $this->prompt;
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
     * Set all parameter definitions
     */
    public function setParameters(array $parameters): void
    {
        foreach ($parameters as $identifier => $parameter) {
            $this->setParameter($identifier, $parameter);
        }
    }

    /**
     * Set a single parameter definition
     */
    private function setParameter(string $identifier, Parameter $parameter): void
    {
        $this->parameters[$identifier] = $parameter;
    }

    /**
     * Return the parameters that have to be inserted.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function isParameterMissing(array $values): bool
    {
        foreach (array_keys($this->getParameters()) as $identifier) {
            if (!array_key_exists($identifier, $values)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return the checksum of the command. This is used to check if a command
     * was changed.
     */
    public function getChecksum(): string
    {
        return md5($this->getPrompt());
    }

    public function getFullyQualifiedIdentifier(): string
    {
        return $this->fullyQualifiedIdentifier;
    }

    public function setFullyQualifiedIdentifier(string $fullyQualifiedIdentifier): void
    {
        $this->fullyQualifiedIdentifier = $fullyQualifiedIdentifier;
    }

    public function formatOutput(string $output): string
    {
        if (!$this->outputFormat) {
            return $output;
        }

        return (sprintf($this->outputFormat, trim($output)));
    }

    public function isAllowedInHistory(): bool
    {
        if (!$this->allowedInHistory) {
            return false;
        }

        foreach ($this->parameters as $parameter) {
            if ($parameter instanceof PasswordParameter) {
                return false;
            }
        }

        return true;
    }

    public function setAllowedInHistory(bool $allowedInHistory): void
    {
        $this->allowedInHistory = $allowedInHistory;
    }

    public function getExplanation(): string
    {
        return $this->explanation;
    }

    public function jsonSerialize(): array
    {
        if ($this->plainCommandArray) {
            return $this->plainCommandArray;
        } else {
            $command = [
                'name' => $this->getName(),
                'description' => $this->getDescription(),
                'prompt' => $this->getPrompt()
            ];
            foreach ($this->getParameters() as $identifier => $parameter) {
                $command['parameters'][$identifier] = $parameter->jsonSerialize();
            }
            return $command;
        }
    }
}
