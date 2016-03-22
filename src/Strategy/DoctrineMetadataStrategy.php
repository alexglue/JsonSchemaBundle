<?php

namespace Soyuka\JsonSchemaBundle\Strategy;

use Doctrine\Common\Persistence\ManagerRegistry;
use ReflectionClass;

/**
 * DoctrineMetadataStrategy generates a json schema from a Doctrine entity
 * it reads annotations.
 */
class DoctrineMetadataStrategy implements StrategyInterface
{
    private $metadataFactory;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->metadataFactory = $doctrine
                            ->getManager()
                            ->getMetadataFactory();
    }

    public function generate(string $class, array $context = []): array
    {
        $required = [];
        $properties = [];

        $fieldMappings = $this->metadataFactory
                            ->getMetadataFor($class)
                            ->fieldMappings;

        foreach ($fieldMappings as $property) {
            $type = null;
            $format = null;

            if (in_array($property['type'], ['decimal', 'float'])) {
                $type = 'number';
            }

            if (in_array($property['type'], ['integer', 'smallint', 'bigint'])) {
                $type = 'integer';
            }

            if (in_array($property['type'], ['simple_array', 'json_array', 'array'])) {
                $type = 'array';
            }

            if (in_array($property['type'], ['text'])) {
                $type = 'string';
            }

            if (in_array($property['type'], ['datetime', 'datetimez'])) {
                $type = 'string';
                $format = 'date-time';
            }

            if (in_array($property['type'], ['date'])) {
                $type = 'string';
                $format = 'date';
            }

            if (in_array($property['type'], ['time'])) {
                $type = 'string';
                $format = 'time';
            }

            //Warn
            if ($type === null) {
                $type = 'string';
            }

            $properties[$property['fieldName']] = [
                'type' => $type,
            ];

            $prop = &$properties[$property['fieldName']];

            if ($format) {
                $prop['format'] = $format;
            }

            if ((isset($property['nullable']) && $property['nullable'] === false) || isset($property['id'])) {
                $required[] = $property['fieldName'];
            }

            /*
             * Validating input data, the id might not be present if a resource doesn't exist yet
             */
            if (isset($property['id'])) {
                $prop['type'] = [$prop['type'], 'null'];
            }

            if (isset($property['length']) && $property['length'] !== null) {
                $prop['maxLength'] = $property['length'];
            }

            if (isset($property['options'])) {
                $opts = &$property['options'];

                if (isset($opts['default'])) {
                    $prop['default'] = $opts['default'];
                }

                if (isset($opts['unsigned']) && $opts['unsigned'] === true) {
                    $prop['minimum'] = 0;
                    $prop['exclusiveMinimum'] = true;
                }
            }
        }

        return [
            'title' => $context['title'] ?? (new ReflectionClass($class))->getShortName(),
            'type' => 'object',
            'properties' => $properties,
            'required' => $required,
        ];
    }
}
