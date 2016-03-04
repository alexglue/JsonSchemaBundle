<?php

namespace Soyuka\JsonSchemaBundle\Strategy;

interface StrategyInterface
{
    /**
     * Generate a json schema.
     *
     * @param string $classname
     * @param array  $context
     *
     * @return array
     */
    public function generate(string $classname, array $context = []): array;
}
