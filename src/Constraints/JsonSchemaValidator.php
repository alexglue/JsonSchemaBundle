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
    private $cache = [];

    public function __construct(ValidatorService $validator, UriRetrieverService $uriRetrieverService, $jsonSchemasPath)
    {
        $this->validator = $validator;
        $this->uriRetrieverService = $uriRetrieverService;
        $this->jsonSchemasPath = $jsonSchemasPath;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($class, Constraint $constraint)
    {
        $schema = $constraint->schema;

        $name = is_string($class) ? $class : get_class($class);

        if ($schema === null) {
            if (!isset($this->cache[$name])) {
                $refl = new \ReflectionClass($class);
                $directory = explode('\\', $refl->getName());
                $name = array_pop($directory);
                $schema = sprintf(
                    'file://%s/%s/%s.json',
                    $this->jsonSchemasPath,
                    implode('', [$directory[0], $directory[1]]),
                    $name
                );

                $this->cache[$name] = $schema;
            } else {
                $schema = $this->cache[$name];
            }
        }

        $baseUri = sprintf('file://%s', $this->jsonSchemasPath);

        $schema = $this->uriRetrieverService->retrieve($schema, $baseUri);

        $valid = $this->validator->isValid($class, $schema);

        if (!$valid) {
            foreach ($this->validator->getErrors() as $error) {
                $this->context
                    ->buildViolation($error->getViolation())
                    ->atPath($error->getProperty())
                    ->addViolation();
            }
        }
    }
}
