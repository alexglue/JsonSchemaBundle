<?php

namespace Soyuka\JsonSchemaBundle\Mapping\Validator;

use Soyuka\JsonSchemaBundle\Error\Error;
use Soyuka\JsonSchemaBundle\Error\ErrorInterface;
use Soyuka\JsonSchemaBundle\Exception\ViolationException;
use JsonSchema\Validator as BaseValidator;

class ValidatorService implements ValidatorServiceInterface
{
    /**
     * @var BaseValidator
     */
    protected $validator = null;

    /**
     * @var ErrorInterface[]
     */
    protected $errors = array();

    public function __construct($validatorClass)
    {
        $this->validator = new $validatorClass();
    }

    public function check($object, $schema)
    {
        $this->reset();

        $this->validator->check($object, $schema);

        if (!$this->validator->isValid()) {
            $this->convertErrors();
            throw new ViolationException($this->errors);
        }

        return true;
    }

    public function isValid($object, $schema)
    {
        $this->reset();

        $this->validator->check($object, $schema);

        $this->convertErrors();

        return $this->validator->isValid();
    }

    public function getErrors()
    {
        return $this->errors;
    }

    protected function convertErrors()
    {
        $this->errors = array();

        foreach ($this->validator->getErrors() as $error) {
            $this->errors[] = new Error($error['property'], $error['message'], $error['constraint']);
        }
    }

    protected function reset()
    {
        $this->errors = array();
        $this->validator->reset();
    }
}
