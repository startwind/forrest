<?php

namespace Startwind\Forrest\Command\Parameters;

use Startwind\Forrest\Command\Parameters\Validation\Constraint\ConstraintFactory;
use Startwind\Forrest\Enrichment\EnrichFunction\String\FunctionComposite;

class ParameterFactory
{
    private const TYPE_MIXED = 'forrest_mixed';
    private const TYPE_FILENAME = 'forrest_filename';
    private const TYPE_PASSWORD = 'forrest_password';
    private const FIELD_TYPE = 'type';
    private const FIELD_CONSTRAINTS = 'constraints';

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
            case self::TYPE_PASSWORD:
                $parameter = new PasswordParameter();
                break;
            default:
                $parameter = self::createMixedParameter($config);
        }

        self::enrichParameters($parameter, $config);

        return $parameter;
    }

    private static function enrichParameters(Parameter $parameter, array $config): void
    {
        $parameter->setRawStructure($config);

        if (array_key_exists('name', $config)) {
            $parameter->setName($config['name']);
        }

        if (array_key_exists('description', $config)) {
            $parameter->setDescription($config['description']);
        }

        if (array_key_exists('default', $config)) {
            if (!is_string($config['default'])) {
                throw new \RuntimeException('The default value must be a string. ' . gettype($config['default']) . ' with value ' . json_encode($config['default']) . ' given.');
            }
            $defaultValue = self::getDefaultValue($config['default']);
            if ($defaultValue !== '') {
                $parameter->setDefaultValue($defaultValue);
            }
        }

        if (array_key_exists(self::FIELD_CONSTRAINTS, $config)) {
            $constraints = self::getConstraints($config[self::FIELD_CONSTRAINTS]);
            $parameter->setConstraints($constraints);
        }

        if (array_key_exists('enum', $config)) {
            $parameter->setValues($config['enum']);
        }
    }

    private static function getConstraints(array $constraintArray): array
    {
        $constraints = [];

        foreach ($constraintArray as $constraint) {
            try {
                if (is_null($constraint)) {
                    throw new \RuntimeException('The given constraint must not be null.');
                }
                $constraints[] = ConstraintFactory::getConstraint($constraint);
            } catch (\Exception $exception) {
                // @todo log error
            }
        }

        return $constraints;
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
