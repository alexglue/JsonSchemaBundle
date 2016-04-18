<?php

namespace Soyuka\JsonSchemaBundle\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Soyuka\JsonSchemaBundle\Mapping\Validator\ValidatorService;
use Soyuka\JsonSchemaBundle\Mapping\Uri\UriRetrieverService;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheException;

final class JsonSchemaValidator extends ConstraintValidator
{
    const CACHE_KEY_PREFIX = 'json_schema_validator_';

    private $validator;
    private $uriRetrieverService;
    private $jsonSchemasPath;
    private $cache = null;
    private $accessor;

    public function __construct(ValidatorService $validator, UriRetrieverService $uriRetrieverService, $jsonSchemasPath)
    {
        $this->validator = $validator;
        $this->uriRetrieverService = $uriRetrieverService;
        $this->jsonSchemasPath = $jsonSchemasPath;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function setCache(CacheItemPoolInterface $cacheItemPool) {
        $this->cache = $cacheItemPool;
    }

    /**
     * Get schema file path for a given class
     * @param mixed $class
     * @return string
     */
    private function getSchemaForClass($class): string
    {
        $cacheKey = self::CACHE_KEY_PREFIX . (is_string($class) ? $class : get_class($class));

        if (null !== $this->cache) {
            try {
                $cacheItem = $this->cache->getItem($cacheKey);
                if ($cacheItem->isHit()) {
                    return $cacheItem->get();
                }
            } catch(CacheException $e) {}
        }

        $refl = new \ReflectionClass($class);
        $directory = explode('\\', $refl->getName());
        $name = array_pop($directory);
        $schema = sprintf(
            'file://%s/%s/%s.json',
            $this->jsonSchemasPath,
            implode('', [$directory[0], $directory[1]]),
            $name
        );

        if (isset($cacheItem)) {
            try {
                $cacheItem->set($schema);
                $this->cache->save($cacheItem);
            } catch(CacheException $e) {}
        }

        return $schema;
    }

    private function propertyToString($v): string {
        if (is_array($v)) {
            return implode(', ', $v);
        }

        return (string) $v;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($class, Constraint $constraint)
    {
        $schema = $constraint->schema;

        if ($schema === null) {
            $schema = $this->getSchemaForClass($class);
        }

        $baseUri = sprintf('file://%s', $this->jsonSchemasPath);

        $schema = $this->uriRetrieverService->retrieve($schema, $baseUri);

        $valid = $this->validator->isValid($class, $schema);

        if (!$valid) {
            foreach ($this->validator->getErrors() as $error) {
                $parameters = [
                    'expected' => $schema->properties->{$error->getProperty()}->{$error->getConstraint()},
                    'invalid' => $this->accessor->getValue($class, $error->getProperty()),
                ];

                $this->context
                    ->buildViolation($error->getConstraint())
                    ->atPath($error->getProperty())
                    ->setCode($error->getConstraint())
                    ->setCause($error->getViolation())
                    ->setInvalidValue($parameters['invalid'])
                    ->setParameter('%expected%', $this->propertyToString($parameters['expected']))
                    ->setParameter('%invalid%', $this->propertyToString($parameters['invalid']))
                    ->setParameter('%property%', $error->getProperty())
                    ->setTranslationDomain('JsonSchemaViolations')
                    ->addViolation();
            }
        }
    }
}
