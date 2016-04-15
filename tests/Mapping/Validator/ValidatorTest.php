<?php

namespace Soyuka\JsonSchemaBundle\tests\Mapping\Validator;

use Soyuka\JsonSchemaBundle\Exception\ViolationException;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Soyuka\JsonSchemaBundle\Validator\ValidatorServiceInterface
     */
    protected $validator;

    /**
     * @var \stdClass
     */
    protected $schema;

    public function setUp()
    {
        $this->validator = new \Soyuka\JsonSchemaBundle\Mapping\Validator\ValidatorService('JsonSchema\Validator');

        $this->schema = new \stdClass();
        $this->schema->schema = 'http://json-schema.org/draft-04/schema#';
        $this->schema->title = 'Product';
        $this->schema->description = 'A product from Acme catalog';
        $this->schema->properties = array();
        $this->schema->properties['id'] = (object) array('type' => 'integer');
        $this->schema->properties['name'] = (object) array('type' => 'string');
        $this->schema->properties['description'] = (object) array('type' => 'string');
        $this->schema->required = array('id', 'name');
    }

    public function testIsValidSuccess()
    {
        $validObject = new \stdClass();
        $validObject->id = 12;
        $validObject->name = 'test-name';
        $validObject->description = 'test-description';

        $this->assertTrue($this->validator->isValid($validObject, $this->schema));
        $this->assertEmpty($this->validator->getErrors());
    }

    public function testIsValidFail()
    {
        $invalidObject = new \stdClass();
        $invalidObject->name = 'test-name';

        $this->assertFalse($this->validator->isValid($invalidObject, $this->schema));
        $this->assertNotEmpty($this->validator->getErrors());

        $errors = $this->validator->getErrors();

        $this->assertEquals('The property id is required', $errors[0]->getViolation());
        $this->assertEquals('id: The property id is required (required)', "{$errors[0]}");

        $invalidObject->id = 42;

        $this->assertTrue($this->validator->isValid($invalidObject, $this->schema));
        $this->assertEmpty($this->validator->getErrors());
    }

    public function testCheckSuccess()
    {
        $validObject = new \stdClass();
        $validObject->id = 12;
        $validObject->name = 'test-name';
        $validObject->description = 'test-description';

        $this->assertTrue($this->validator->check($validObject, $this->schema));
        $this->assertEmpty($this->validator->getErrors());
    }

    public function testCheckFail()
    {
        $invalidObject = new \stdClass();
        $invalidObject->name = 'test-name';

        try {
            $this->validator->check($invalidObject, $this->schema);
        } catch(ViolationException $e) {
            $this->assertNotEmpty($e->getErrors());
        }

        $this->assertNotEmpty($this->validator->getErrors());

        $errors = $this->validator->getErrors();

        $this->assertEquals('The property id is required', $errors[0]->getViolation());
        $this->assertEquals('id: The property id is required (required)', "{$errors[0]}");

        $invalidObject->id = 42;

        $this->assertTrue($this->validator->check($invalidObject, $this->schema));
        $this->assertEmpty($this->validator->getErrors());
    }
}
