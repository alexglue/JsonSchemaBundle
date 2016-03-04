<?php

namespace Soyuka\JsonSchemaBundle\Tests\Fixtures;

use Soyuka\JsonSchemaBundle\Constraints as JsonSchemaAssert;

/**
 * @JsonSchemaAssert\JsonSchema
 */
class DefaultJsonSchema
{
    private $id;
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
