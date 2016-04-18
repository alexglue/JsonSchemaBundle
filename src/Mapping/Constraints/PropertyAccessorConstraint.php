<?php

namespace Soyuka\JsonSchemaBundle\Mapping\Constraints;

use JsonSchema\Constraints\ObjectConstraint;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Constraints\Factory;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyAccessorConstraint extends ObjectConstraint
{
    private $accessor;

    public function __construct($checkMode = self::CHECK_MODE_NORMAL, UriRetriever $uriRetriever = null, Factory $factory = null)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        parent::__construct($checkMode, $uriRetriever, $factory);
    }

    /**
     * retrieves a property from an object or array.
     *
     * @param mixed  $element  Element to validate
     * @param string $property Property to retrieve
     * @param mixed  $fallback Default value if property is not found
     *
     * @return mixed
     */
    protected function getProperty($element, $property, $fallback = null)
    {
        if (is_array($element) /*$this->checkMode == self::CHECK_MODE_TYPE_CAST*/) {
            return array_key_exists($property, $element) ? $element[$property] : $fallback;
        } elseif (is_object($element)) {
            return $this->accessor->isReadable($element, $property) ? $this->accessor->getValue($element, $property) : $fallback;
        }

        return $fallback;
    }
}
