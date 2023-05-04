<?php

namespace Startwind\Forrest\Command\Parameters;

class ParameterFactory
{
    const TYPE_MIXED = 'forrest_mixed';
    const TYPE_FILENAME = 'forrest_filename';
    const FIELD_TYPE = 'type';

    /**
     * Create a Parameter configuration object from the given config array.
     */
    static public function create(array $config): Parameter
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

    static private function enrichParameters(Parameter $parameter, array $config): void
    {
        if (array_key_exists('name', $config)) {
            $parameter->setName($config['name']);
        }

        if (array_key_exists('description', $config)) {
            $parameter->setDescription($config['description']);
        }

        if (array_key_exists('default', $config)) {
            $parameter->setDefaultValue($config['default']);
        }
    }

    static private function createMixedParameter(array $config): Parameter
    {
        if (empty($config)) {
            return new UndefinedParameter();
        } else {
            return new Parameter();
        }
    }

    static private function createFileParameter($config): FileParameter
    {
        $fileParameter = new FileParameter();

        if (array_key_exists('file-formats', $config)) {
            $fileParameter->setFileFormats($config['file-formats']);
        }

        return $fileParameter;
    }
}