<?php

namespace Soyuka\JsonSchemaBundle\tests\Strategy;

use Soyuka\JsonSchemaBundle\Tests\KernelTrait;
use Soyuka\JsonSchemaBundle\Strategy\DoctrineMetadataStrategy;

class DoctrineMetadataStrategyTest extends \PHPUnit_Framework_TestCase
{
    use KernelTrait;

    private $doctrineMetadataStrategy;

    public function setUp()
    {
        $this->boot();
        $this->doctrineMetadataStrategy = $this->kernel->getContainer()->get('json_schema.doctrine_strategy');
    }

    public function testResult()
    {
        $schema = $this->doctrineMetadataStrategy->generate('Soyuka\JsonSchemaBundle\Tests\Fixtures\TestBundle\Entity\Product');

        $this->assertEquals($schema, [
        'title' => 'Product',
        'type' => 'object',
        'properties' => [
            'id' => [
                'type' => ['integer', 'null'],
            ],
            'name' => [
                'type' => 'string'
            ],
            'description' => [
                'type' => ['string', 'null'],
            ],
            'price' => [
                'type' => ['number', 'null'],
            ],
        ],
        'required' => ['id', 'name'],
        ]);
    }
}
