<?php

namespace Soyuka\JsonSchemaBundle\tests\Constraints;

use Soyuka\JsonSchemaBundle\Tests\Fixtures\DefaultJsonSchema;
use Soyuka\JsonSchemaBundle\Tests\Fixtures\SpecificJsonSchema;
use Soyuka\JsonSchemaBundle\Tests\KernelTrait;
use Soyuka\JsonSchemaBundle\Tests\Fixtures\TestBundle\Entity\Product;
use Doctrine\ORM\Tools\SchemaTool;

class JsonSchemaValidatorTest extends \PHPUnit_Framework_TestCase
{
    use KernelTrait;
    private $validator;
    private $manager;
    private $schema;
    private $classes;

    public function setUp()
    {
        $this->boot();
        $this->validator = $this->getContainer()->get('validator');

        $doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $doctrine->getManager();

        $this->schema = new SchemaTool($this->manager);
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();

        $this->schema->createSchema($this->classes);
    }

    public function tearDown()
    {
        $this->schema->dropSchema($this->classes);
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

    public function testTranslated()
    {
        $entity = new DefaultJsonSchema();
        $entity->setName('o');
        $errors = $this->validator->validate($entity);

        $this->assertEquals($errors->get(0)->getMessage(), 'name is not long enough, expected 2 got "o"');
        $this->assertEquals(1, count($errors));
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

    public function testDoctrineProxyClass()
    {
        $this->markTestSkipped('This fails');
        $this->boot('test');

        $product = new Product();
        $product->setName('something');
        $this->manager->persist($product);
        $this->manager->flush();

        $proxy = $this->manager->getProxyFactory()->getProxy('Soyuka\JsonSchemaBundle\Tests\Fixtures\TestBundle\Entity\Product', ['id' => 1]);

        $this->validator = $this->getContainer()->get('validator');

        $errors = $this->validator->validate($proxy);
    }
}
