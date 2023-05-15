<?php

namespace Startwind\Forrest\Command;

use Startwind\Forrest\Command\Parameters\ParameterValue;
use Startwind\Forrest\Command\Parameters\PasswordParameter;
use Startwind\Forrest\Enrichment\EnrichFunction\FunctionComposite;

class Prompt
{
    public const PARAMETER_PREFIX = '${';
    public const PARAMETER_POSTFIX = '}';


    private string $plainPrompt;
    private string $finalPrompt = '';
    private array $values;

    private string $securePrompt;

    private bool $isStorable = true;

    /**
     * @param ParameterValue[] $values
     */
    public function __construct(string $plainPrompt, array $values)
    {
        $this->plainPrompt = $plainPrompt;
        $this->values = $values;

        foreach ($values as $value) {
            $this->finalPrompt = str_replace(self::PARAMETER_PREFIX . $value->getKey() . self::PARAMETER_POSTFIX, $value->getValue(), $plainPrompt);

            if ($value->getType() == PasswordParameter::TYPE) {
                $this->isStorable = false;
                $this->securePrompt = str_replace(self::PARAMETER_PREFIX . $value->getKey() . self::PARAMETER_POSTFIX, '***', $plainPrompt);
            } else {
                $this->securePrompt = str_replace(self::PARAMETER_PREFIX . $value->getKey() . self::PARAMETER_POSTFIX, $value->getValue(), $plainPrompt);
            }
        }

        $function = new FunctionComposite();
        $this->finalPrompt = $function->applyFunction($this->finalPrompt);
    }

    public function getPlainPrompt(): string
    {
        return $this->plainPrompt;
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

    public function getSecurePrompt(): string
    {
        return $this->securePrompt;
    }

    /**
     * @return bool
     */
    public function isStorable(): bool
    {
        return $this->isStorable;
    }
}
