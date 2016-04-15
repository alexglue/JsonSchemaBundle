<?php

namespace Soyuka\JsonSchemaBundle\Error;

class Error implements ErrorInterface
{
    protected $property;
    protected $violation;
    protected $constraint;

    /**
     * @param string $property
     * @param string $violation
     */
    public function __construct($property, $violation, $constraint)
    {
        $this->property = $property;
        $this->violation = $violation;
        $this->constraint = $constraint;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function getViolation()
    {
        return $this->violation;
    }

    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('%s: %s (%s)', $this->property, $this->violation, $this->constraint);
    }
}
