<?php

namespace Startwind\Forrest\Command\Parameters;

interface ParameterInterface
{
    public function getName(): string;
    public function setName(string $name): void;

    public function getDescription(): string;
    public function setDescription(string $description): void;

    public function getDefaultValue(): string;
    public function setDefaultValue(string $defaultValue): void;

    public function getValues(): array;
    public function setValues(array $values): void;
    public function hasValues(): bool;
}
