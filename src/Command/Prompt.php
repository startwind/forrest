<?php

namespace Startwind\Forrest\Command;

use Startwind\Forrest\Command\Parameters\ParameterValue;
use Startwind\Forrest\Command\Parameters\PasswordParameter;
use Startwind\Forrest\Enrichment\EnrichFunction\FunctionComposite;

class Prompt
{
    public const PARAMETER_PREFIX = '${';
    public const PARAMETER_POSTFIX = '}';

    private string $finalPrompt = '';
    private array $values;

    private string $securePrompt;

    /**
     * @param ParameterValue[] $values
     */
    public function __construct(string $plainPrompt, array $values)
    {
        $this->values = $values;

        $this->finalPrompt = $plainPrompt;
        $this->securePrompt = $plainPrompt;

        foreach ($values as $value) {
            $this->finalPrompt = str_replace(self::PARAMETER_PREFIX . $value->getKey() . self::PARAMETER_POSTFIX, $value->getValue(), $this->finalPrompt);

            if ($value->getType() == PasswordParameter::TYPE) {
                $staredPassword = str_repeat('*', strlen($value->getValue()));
                $this->securePrompt = str_replace(self::PARAMETER_PREFIX . $value->getKey() . self::PARAMETER_POSTFIX, $staredPassword, $this->securePrompt);
            } else {
                $this->securePrompt = str_replace(self::PARAMETER_PREFIX . $value->getKey() . self::PARAMETER_POSTFIX, $value->getValue(), $this->securePrompt);
            }
        }

        $function = new FunctionComposite();
        $this->finalPrompt = $function->applyFunction($this->finalPrompt);
        $this->securePrompt = $function->applyFunction($this->securePrompt);
    }

    /**
     * @return string
     */
    public function getFinalPrompt(): string
    {
        return $this->finalPrompt;
    }

    /**
     * @return ParameterValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * This function returns the prompt but hides passwords.
     */
    public function getSecurePrompt(): string
    {
        return $this->securePrompt;
    }
}
