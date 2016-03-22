<?php

namespace Soyuka\JsonSchemaBundle\tests\Constraints;

use Soyuka\JsonSchemaBundle\Tests\Fixtures\DefaultJsonSchema;
use Soyuka\JsonSchemaBundle\Tests\Fixtures\SpecificJsonSchema;
use Soyuka\JsonSchemaBundle\Tests\KernelTrait;

class JsonSchemaValidatorTest extends \PHPUnit_Framework_TestCase
{
    use KernelTrait;
    private $validator;

    public function setUp()
    {
        $this->boot();
        $this->validator = $this->getContainer()->get('validator');
    }

    public function testDefaultSchemaInvalid()
    {
        $entity = new DefaultJsonSchema();
        $errors = $this->validator->validate($entity);

        $this->assertEquals(1, count($errors));
    }

    public function testDefaultSchemaInvalidName()
    {
        $entity = new DefaultJsonSchema();
        $entity->setName('');
        $errors = $this->validator->validate($entity);

        $this->assertEquals(1, count($errors));
    }

    public function testDefaultSchemaValid()
    {
        $entity = new DefaultJsonSchema();
        $entity->setName('Test');
        $errors = $this->validator->validate($entity);

        $this->assertEquals(0, count($errors));
    }

    public function testSpecificSchemaInvalid()
    {
        $entity = new SpecificJsonSchema();
        $errors = $this->validator->validate($entity);

        $this->assertEquals(3, count($errors));
    }

    public function testSpecificSchemaInvalidEnum()
    {
        $entity = new SpecificJsonSchema();
        $entity->setName('Test');
        $entity->setEnum('Foobar');
        $errors = $this->validator->validate($entity);

        $this->assertEquals(1, count($errors));
    }

    public function testSpecificSchemaValid()
    {
        $entity = new SpecificJsonSchema();
        $entity->setName('Test');
        $entity->setEnum('foo');
        $errors = $this->validator->validate($entity);

        $this->assertEquals(0, count($errors));
    }

    /**
     * @expectedException JsonSchema\Exception\UriResolverException
     */
    public function testPathConfigurationChange()
    {
        $this->boot('testbis');

        $this->validator = $this->getContainer()->get('validator');

        $entity = new SpecificJsonSchema();
        $errors = $this->validator->validate($entity);
    }
}
