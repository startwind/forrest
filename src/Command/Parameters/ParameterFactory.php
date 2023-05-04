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
                return self::createFileParameter($config);
            default:
                return self::createMixedParameter($config);
        }
    }

    static private function createMixedParameter(array $config): Parameter
    {
        if (empty($config)) {
            return new UndefinedParameter();
        } else {
            return new UndefinedParameter();
        }
    }

    static private function createFileParameter($config): FileParameter
    {
        $fileParameter = new FileParameter();

        if (array_key_exists('name', $config)) {
            $fileParameter->setName($config['name']);
        }

        if (array_key_exists('description', $config)) {
            $fileParameter->setDescription($config['description']);
        }

        if (array_key_exists('file-formats', $config)) {
            $fileParameter->setFileFormats($config['file-formats']);
        }

        return $fileParameter;
    }
}
