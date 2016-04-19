<?php

namespace Soyuka\JsonSchemaBundle\Mapping\Constraints;

use JsonSchema\Constraints\UndefinedConstraint;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Constraints\Factory;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ProxyUndefinedConstraint extends UndefinedConstraint
{
    private $accessor;

    public function __construct($checkMode = self::CHECK_MODE_NORMAL, UriRetriever $uriRetriever = null, Factory $factory = null)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        parent::__construct($checkMode, $uriRetriever, $factory);
    }

    protected function validateCommonProperties($value, $schema = null, $path = null, $i = "")
    {
        if(is_object($value)) {
			if (!($value instanceof UndefinedConstraint) && isset($schema->required) && is_array($schema->required) ) {
                // Draft 4 - Required is an array of strings - e.g. "required": ["foo", ...]
                foreach ($schema->required as $required) {
                    //@TODO
                    if($value instanceof \Doctrine\Common\Persistence\Proxy) {
                        continue;
                    }

                    if (!property_exists($value, $required)) {
                        $this->addError((!$path) ? $required : "$path.$required", "The property " . $required . " is required", 'required');
                    }
                }
            } else if (isset($schema->required) && !is_array($schema->required)) {
                // Draft 3 - Required attribute - e.g. "foo": {"type": "string", "required": true}
                if ( $schema->required && $value instanceof UndefinedConstraint) {
                    $this->addError($path, "Is missing and it is required", 'required');
                }
            }
        } else {
            return parent::validateCommonProperties($value, $schema, $path, $i);
        }
    }

}

