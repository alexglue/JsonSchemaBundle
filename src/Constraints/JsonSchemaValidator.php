<?php

namespace Soyuka\JsonSchemaBundle\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Soyuka\JsonSchemaBundle\Mapping\Validator\ValidatorService;
use Soyuka\JsonSchemaBundle\Mapping\Uri\UriRetrieverService;

class JsonSchemaValidator extends ConstraintValidator
{
    private $validator;
    private $uriRetrieverService;
    private $jsonSchemasPath;
    private $cache = null;

    public function __construct(ValidatorService $validator, UriRetrieverService $uriRetrieverService, $jsonSchemasPath)
    {
        $this->validator = $validator;
        $this->uriRetrieverService = $uriRetrieverService;
        $this->jsonSchemasPath = $jsonSchemasPath;
    }

    /**
     * Get schema file path for a given class
     * @param mixed $class
     * @TODO implement cache
     * @return string
     */
    public function getSchemaForClass($class): string
    {
        // $cacheName = is_string($class) ? $class : get_class($class);

        // if (isset($this->cache[$cacheName])) {
        //     return $this->cache[$cacheName];
        // }

        $refl = new \ReflectionClass($class);
        $directory = explode('\\', $refl->getName());
        $name = array_pop($directory);
        $schema = sprintf(
            'file://%s/%s/%s.json',
            $this->jsonSchemasPath,
            implode('', [$directory[0], $directory[1]]),
            $name
        );

        return $schema;
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
                $this->context
                    ->buildViolation((string) $error)
                    ->atPath($error->getProperty())
                    ->setCode($error->getConstraint())
                    ->setCause($error->getViolation())
                    ->setTranslationDomain('JsonSchema')
                    ->addViolation();
            }
        }
    }
}
