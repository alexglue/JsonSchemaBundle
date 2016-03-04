<?php

namespace Soyuka\JsonSchemaBundle\Tests\Fixtures;

use Soyuka\JsonSchemaBundle\Constraints as JsonSchemaAssert;

/**
 * @JsonSchemaAssert\JsonSchema(schema = "validators/specific.json")
 */
class SpecificJsonSchema
{
    private $id;
    private $name;
    private $enum;

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

    public function getEnum()
    {
        return $this->enum;
    }

    public function setEnum($enum)
    {
        $this->enum = $enum;
    }
}
