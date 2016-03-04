<?php

namespace Soyuka\JsonSchemaBundle\Mapping\Validator;

use Soyuka\JsonSchemaBundle\Error\ErrorInterface;
use Soyuka\JsonSchemaBundle\Exception\ViolationException;

interface ValidatorServiceInterface
{
    /**
     * Checks given object against schema. Throws an ViolationException when given object doesn't match schema.
     * Check and isValid methods are similar, but in case of violations method check throws exception, and isValid returns false.
     *
     * @param mixed $object
     * @param mixed $schema
     *
     * @throws ViolationException
     *
     * @return true
     */
    public function check($object, $schema);

    /**
     * @param mixed $object
     * @param mixed $schema
     *
     * @return bool
     */
    public function isValid($object, $schema);

    /**
     * @return ErrorInterface[]
     */
    public function getErrors();
}
