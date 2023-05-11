<?php

namespace Startwind\Forrest\Command\Parameters;

use Startwind\Forrest\Enrichment\EnrichFunction\FunctionComposite;

class ParameterFactory
{
    public const TYPE_MIXED = 'forrest_mixed';
    public const TYPE_FILENAME = 'forrest_filename';
    public const FIELD_TYPE = 'type';

    public const DEFAULT_ENV_PATTERN = '^\${ENV\((.*)\)}^';

    /**
     * Create a Parameter configuration object from the given config array.
     */
    public static function create(array $config): Parameter
    {
        if (array_key_exists(self::FIELD_TYPE, $config)) {
            $type = $config[self::FIELD_TYPE];
        } else {
            $type = self::TYPE_MIXED;
        }

        switch ($type) {
            case self::TYPE_FILENAME:
                $parameter = self::createFileParameter($config);
                break;
            default:
                $parameter = self::createMixedParameter($config);
        }

        self::enrichParameters($parameter, $config);

        return $parameter;
    }

    private static function enrichParameters(Parameter $parameter, array $config): void
    {
        if (array_key_exists('name', $config)) {
            $parameter->setName($config['name']);
        }

        if (array_key_exists('description', $config)) {
            $parameter->setDescription($config['description']);
        }

        if (array_key_exists('default', $config)) {
            $defaultValue = self::getDefaultValue($config['default']);
            if ($defaultValue !== '') {
                $parameter->setDefaultValue($defaultValue);
            }
        }

        if (array_key_exists('enum', $config)) {
            $parameter->setValues($config['enum']);
        }
    }

    /**
     * Get the default value. This also handles ENV variables
     */
    private static function getDefaultValue($configElement): string
    {
        $functionComposite = new FunctionComposite();
        return $functionComposite->applyFunction($configElement);
    }

    private static function createMixedParameter(array $config): Parameter
    {
        if (empty($config)) {
            return new UndefinedParameter();
        } else {
            return new Parameter();
        }
    }

    private static function createFileParameter($config): FileParameter
    {
        $fileParameter = new FileParameter();

        if (array_key_exists('file-formats', $config)) {
            $fileParameter->setFileFormats($config['file-formats']);
        }

        return $fileParameter;
    }
}
