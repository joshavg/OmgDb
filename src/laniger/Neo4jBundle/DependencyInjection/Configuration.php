<?php
namespace laniger\Neo4jBundle\DependencyInjection;

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
     *
     * @ERROR!!!
     *
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('laniger_neo4j');

        $rootNode->children()
            ->scalarNode('user')
            ->end()
            ->scalarNode('pw')
            ->end()
            ->integerNode('port')
            ->end()
            ->scalarNode('host')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
