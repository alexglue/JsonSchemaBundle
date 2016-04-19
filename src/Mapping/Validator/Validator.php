<?php

namespace Soyuka\JsonSchemaBundle\Mapping\Validator;

use JsonSchema\Validator as BaseValidator;
use JsonSchema\Constraints\Factory;
use JsonSchema\Uri\UriRetriever;

class Validator extends BaseValidator
{
    public function __construct($checkMode = self::CHECK_MODE_NORMAL, UriRetriever $uriRetriever = null, Factory $factory = null)
    {
        parent::__construct();

        $this->getFactory()
            ->setConstraintClass('object', 'Soyuka\JsonSchemaBundle\Mapping\Constraints\PropertyAccessorConstraint')
            ->setConstraintClass('undefined', 'Soyuka\JsonSchemaBundle\Mapping\Constraints\ProxyUndefinedConstraint');
    }
}
