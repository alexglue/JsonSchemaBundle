<?php

namespace Soyuka\JsonSchemaBundle\Strategy;

use Dunglas\PhpToJsonSchema\Generator;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * DoctrineMetadataStrategy generates a json schema from php files
 * it uses dunglas/php-to-json-schema
 */
class PhpStrategy implements StrategyInterface
{
    private $generator;

    public function __construct(PropertyInfoExtractor $propertyInfoExtractor)
    {
        $this->generator = new Generator($propertyInfoExtractor);
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $class, array $context = []): array
    {
        return $this->generator->generate($class, $context);
    }
}
