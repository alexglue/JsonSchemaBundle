<?php

namespace Soyuka\JsonSchemaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('json_schema');

        $rootNode
            ->children()
                ->scalarNode('path')
                    ->defaultValue('%kernel.root_dir%/Resources/validators')
                    ->info('The directory that contains entity json schemas')
                ->end()
                ->scalarNode('cache')
                    ->info('Cache service')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
