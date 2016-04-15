<?php

namespace Soyuka\JsonSchemaBundle\DependencyInjection;

use Soyuka\JsonSchemaBundle\DependencyInjection\JsonSchemaExtension;
use Prophecy\Argument;
use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class JsonSchemaExtensionTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_CONFIG = [
        'json_schema' => [
            'path' => 'test'
        ],
    ];

    private $extension;

    public function setUp()
    {
        $this->extension = new JsonSchemaExtension();
    }

    public function tearDown()
    {
        unset($this->extension);
    }

    public function testLoadDefaultConfig()
    {
        $containerBuilderProphecy = $this->getContainerBuilderProphecy();
        $containerBuilder = $containerBuilderProphecy->reveal();

        $this->extension->load(self::DEFAULT_CONFIG, $containerBuilder);
    }

    private function getContainerBuilderProphecy()
    {
        $definitionArgument = Argument::that(function ($argument) {
            return $argument instanceof Definition || $argument instanceof DefinitionDecorator;
        });

        $containerBuilderProphecy = $this->prophesize(ContainerBuilder::class);

        $parameters = [
			'json_schema.validator.class' => 'Soyuka\JsonSchemaBundle\Mapping\Validator\Validator',
			'json_schema.validator.service.class' => 'Soyuka\JsonSchemaBundle\Mapping\Validator\ValidatorService',
			'json_schema.uri_resolver.class' => 'JsonSchema\Uri\UriResolver',
			'json_schema.uri_resolver.service.class' => 'Soyuka\JsonSchemaBundle\Mapping\Uri\UriResolverService',
			'json_schema.uri_retriever.class' => 'JsonSchema\Uri\UriRetriever',
			'json_schema.uri_retriever.service.class' => 'Soyuka\JsonSchemaBundle\Mapping\Uri\UriRetrieverService'
        ];

        foreach ($parameters as $key => $value) {
            $containerBuilderProphecy->hasParameter($key)->shouldBeCalled();
            $containerBuilderProphecy->setParameter($key, $value)->shouldBeCalled();
        }

        $containerBuilderProphecy->setParameter('json_schema.path', 'test')->shouldBeCalled();

        $containerBuilderProphecy->addResource(Argument::type(ResourceInterface::class))->shouldBeCalled();

      $definitions = [
            'json_schema.validator',
            'json_schema.uri_resolver',
            'json_schema.uri_retriever',
            'json_schema.validator_constraint',
            'json_schema.reflection_extractor',
            'json_schema.property_info_extractor',
            'json_schema.php_strategy',
            'json_schema.doctrine_strategy'
        ];

        foreach ($definitions as $definition) {
            $containerBuilderProphecy->setDefinition($definition, $definitionArgument)->shouldBeCalled();
        }

        return $containerBuilderProphecy;
    }
}
