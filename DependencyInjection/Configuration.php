<?php

namespace Rz\PageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('rz_page');
        $this->addManagerSection($node);
        $this->addAdminSection($node);
        $this->addClassSection($node);
        return $treeBuilder;
    }

     /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addManagerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('manager_class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('site')->defaultValue('Rz\\PageBundle\\Entity\\SiteManager')->end()
                                ->scalarNode('snapshot')->defaultValue('Rz\\PageBundle\\Entity\\SnapshotManager')->end()
                                ->scalarNode('page')->defaultValue('Rz\\PageBundle\\Entity\\PageManager')->end()
                                ->scalarNode('block')->defaultValue('Rz\\PageBundle\\Entity\\BlockManager')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addClassSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('page')
                            ->defaultValue('AppBundle\\Entity\\Page\\Page')
                        ->end()
                        ->scalarNode('snapshot')
                            ->defaultValue('AppBundle\\Entity\\Page\\Snapshot')
                        ->end()
                        ->scalarNode('block')
                            ->defaultValue('AppBundle\\Entity\\Page\\Block')
                        ->end()
                        ->scalarNode('site')
                            ->defaultValue('AppBundle\\Entity\\Page\\Site')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }


     private function addAdminSection(ArrayNodeDefinition $node) {
        $node
            ->children()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('site')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\MediaAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:SiteAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('snapshot')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\SnapshotAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:SnapshotAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\PageAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:PageAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('block')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\BlockAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:BlockAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('shared_block')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\SharedBlockAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:BlockAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
