<?php

namespace Soyuka\JsonSchemaBundle\Error;

interface ErrorInterface
{
    /**
     * @return string
     */
    public function getProperty();

    /**
     * @return string
     */
    public function getViolation();

    /**
     * @return string
     */
    public function __toString();
}
