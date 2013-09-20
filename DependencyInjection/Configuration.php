<?php

namespace Presta\ComposerPublicBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('presta_composer_public');

        $rootNode
            ->children()
                ->booleanNode('symlink')->defaultTrue()->info('create a symlink in PrestaComposerPublicBundle folder or a hardcopy.')->end()
                ->arrayNode('blend')
                    ->info('Without parameters the key is used to detect vendor & name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('vendor')->cannotBeEmpty()->info('vendor as it is configured in composer.json')->end()
                        ->scalarNode('name')->cannotBeEmpty()->info('name as it is configured in composer.json')->end()
                        ->scalarNode('path')->cannotBeEmpty()->defaultValue('/')->info('the path you want to be public (related to library root folder)')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
