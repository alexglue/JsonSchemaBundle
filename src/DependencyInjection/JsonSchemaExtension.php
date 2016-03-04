<?php

namespace Soyuka\JsonSchemaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class JsonSchemaExtension extends Extension
{
    //This parameters, prefixed by json_schema are overridable
    private $parameters = [
        'validator.class' => 'Soyuka\JsonSchemaBundle\Mapping\Validator\Validator',
        'validator.service.class' => 'Soyuka\JsonSchemaBundle\Mapping\Validator\ValidatorService',
        'uri_resolver.class' => 'JsonSchema\Uri\UriResolver',
        'uri_resolver.service.class' => 'Soyuka\JsonSchemaBundle\Mapping\Uri\UriResolverService',
        'uri_retriever.class' => 'JsonSchema\Uri\UriRetriever',
        'uri_retriever.service.class' => 'Soyuka\JsonSchemaBundle\Mapping\Uri\UriRetrieverService',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        foreach ($this->parameters as $key => $defaultValue) {
            $key = sprintf('json_schema.%s', $key);
            $container->setParameter($key, $container->hasParameter($key) ? $container->getParameter($key) : $defaultValue);
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('json_schema.path', $config['path']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (class_exists('Doctrine\ORM\Version')) {
            $loader->load('doctrine.yml');
        }
    }
}
