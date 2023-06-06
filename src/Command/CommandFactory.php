<?php

namespace Startwind\Forrest\Command;

use Startwind\Forrest\Command\Parameters\Parameter;
use Startwind\Forrest\Command\Parameters\ParameterFactory;

class CommandFactory
{
    public const CONFIG_FIELD_PROMPT = 'prompt';
    private const CONFIG_FIELD_NAME = 'name';
    private const CONFIG_FIELD_DESCRIPTION = 'description';
    public const CONFIG_FIELD_EXPLANATION = 'explanation';
    public const CONFIG_FIELD_PARAMETERS = 'parameters';
    public const CONFIG_FIELD_ALLOWED_IN_HISTORY = 'allowed-in-history';
    public const CONFIG_FIELD_OUTPUT = 'output-format';

    /**
     * Create a Command object out of the given array.
     */
    public static function fromArray(array $commandConfig, $withParameters = true): Command
    {
        $prompt = $commandConfig[self::CONFIG_FIELD_PROMPT];

        if (array_key_exists(self::CONFIG_FIELD_EXPLANATION, $commandConfig)) {
            $explanation = $commandConfig[self::CONFIG_FIELD_EXPLANATION];
        } else {
            $explanation = '';
        }

        $command = new Command($commandConfig[self::CONFIG_FIELD_NAME], $commandConfig[self::CONFIG_FIELD_DESCRIPTION], $prompt, $explanation);

        $command->setPlainCommandArray($commandConfig);

        if (array_key_exists(self::CONFIG_FIELD_OUTPUT, $commandConfig)) {
            $command->setOutputFormat($commandConfig[self::CONFIG_FIELD_OUTPUT]);
        }

        if (array_key_exists(self::CONFIG_FIELD_ALLOWED_IN_HISTORY, $commandConfig) && $commandConfig[self::CONFIG_FIELD_ALLOWED_IN_HISTORY] === false) {
            $command->setAllowedInHistory(false);
        }

        if (array_key_exists(self::CONFIG_FIELD_PARAMETERS, $commandConfig)) {
            $parameterConfig = $commandConfig[self::CONFIG_FIELD_PARAMETERS];
        } else {
            $parameterConfig = [];
        }

        if (is_string($parameterConfig)) {
            throw new \RuntimeException('The configuration is malformed. Array expected but "' . $parameterConfig . '" found.');
        }

        if ($withParameters) {
            $command->setParameters(self::createParameters($prompt, $parameterConfig));
        }

        return $command;
    }

    /**
     * Create the parameter objects from the array.
     *
     * @return Parameter[]
     */
    private static function createParameters(string $prompt, array $parameterConfig): array
    {
        $parameterNames = self::extractParametersFromPrompt($prompt);

        $parameters = [];

        foreach ($parameterNames as $parameterName) {
            if (array_key_exists($parameterName, $parameterConfig)) {
                $config = $parameterConfig[$parameterName];
            } else {
                $config = [];
            }
            $parameters[$parameterName] = ParameterFactory::create($config);
        }

        return $parameters;
    }

    /**
     * Use regular expressions to extract the parameters from the prompt.
     */
    private static function extractParametersFromPrompt(string $prompt): array
    {
        preg_match_all('^\${[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*}^', $prompt, $matches);

        $parameters = [];

        foreach ($matches[0] as $match) {
            $parameters[] = str_replace(Parameter::PARAMETER_PREFIX, '', str_replace(Parameter::PARAMETER_POSTFIX, '', $match));
        }

        return $parameters;
    }
}
