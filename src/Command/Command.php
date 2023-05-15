<?php

namespace Startwind\Forrest\Command;

use Startwind\Forrest\Command\Parameters\Parameter;
use Startwind\Forrest\Enrichment\EnrichFunction\EnrichFunction;
use Startwind\Forrest\Enrichment\EnrichFunction\FunctionComposite;

class Command
{
    private bool $isRunnable = true;

    private string $fullyQualifiedIdentifier = '';

    private string $outputFormat = '';

    public function setOutputFormat(string $output): void
    {
        $this->outputFormat = $output;
    }

    /**
     * @var Parameter[]
     */
    private array $parameters = [];

    /**
     * @var EnrichFunction[]
     */
    private array $functions;

    public function __construct(private readonly string $name, private readonly string $description, private readonly string $prompt)
    {
        $this->functions = [
            new FunctionComposite()
        ];
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
}
