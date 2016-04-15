<?php

namespace Soyuka\JsonSchemaBundle\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class JsonSchema extends Constraint
{
    public $message = 'The json schema does not validate the input data';
    public $schema = null;

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'json_schema.constraint_validator';
    }
}
