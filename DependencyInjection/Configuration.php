<?php

/*
 * This file is part of the RzPageBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('rz_page');

        $this->addBundleSettings($node);

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }

     /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addBundleSettings(ArrayNodeDefinition $node)
    {
        /**
         * TODO: refactor as not to copy the whole configuration of SonataUserBundle
         * This section will allow RzBundle to override SonataUserBundle via rz_user configuration
         */

        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('block')->defaultValue('Application\\Sonata\\PageBundle\\Entity\\Block')->end()
                        ->scalarNode('page')->defaultValue('Application\\Sonata\\PageBundle\\Entity\\Page')->end()
                        ->scalarNode('site')->defaultValue('Application\\Sonata\\PageBundle\\Entity\\Site')->end()
                        ->scalarNode('snapshot')->defaultValue('Application\\Sonata\\PageBundle\\Entity\\Snapshot')->end()
                    ->end()
                ->end()
                ->arrayNode('class_manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('block')->defaultValue('Sonata\\PageBundle\\Entity\\BlockManager')->end()
                        ->scalarNode('page')->defaultValue('Sonata\\PageBundle\\Entity\\PageManager')->end()
                        ->scalarNode('site')->defaultValue('Sonata\\PageBundle\\Entity\\SiteManager')->end()
                        ->scalarNode('snapshot')->defaultValue('Sonata\\PageBundle\\Entity\\SnapshotManager')->end()
                    ->end()
                ->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('block')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\BlockAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('RzPageBundle:BlockAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('list')->defaultValue('RzPageBundle:BlockAdmin:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('RzPageBundle:BlockAdmin:edit.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\PageAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('RzPageBundle:PageAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('list')->defaultValue('RzPageBundle:PageAdmin:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('RzPageBundle:PageAdmin:edit.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('site')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\SiteAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:SiteAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('list')->defaultValue('SonataAdminBundle:CRUD:list.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('snapshot')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\SnapshotAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:SnapshotAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataPageBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('list')->defaultValue('RzPageBundle:SnapshotAdmin:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('RzPageBundle:SnapshotAdmin:edit.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
