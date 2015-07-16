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
        $this->addBlockSettings($node);
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
                        ->scalarNode('media')->defaultValue('Application\\Sonata\\MediaBundle\\Entity\\Media')->end()
                        ->scalarNode('page_service_default')->defaultValue('Rz\\PageBundle\\Page\\Service\\DefaultPageService')->end()
                        ->scalarNode('page_transformer')->defaultValue('Rz\\PageBundle\\Entity\\Transformer')->end()
                    ->end()
                ->end()
                ->arrayNode('class_manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('block')->defaultValue('Sonata\\PageBundle\\Entity\\BlockManager')->end()
                        ->scalarNode('page')->defaultValue('Rz\\PageBundle\\Entity\\PageManager')->end()
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
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('RzPageBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('user_block')->defaultValue('SonataAdminBundle:Core:user_block.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('add_block')->defaultValue('SonataAdminBundle:Core:add_block.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('layout')->defaultValue('SonataAdminBundle::standard_layout.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('ajax')->defaultValue('SonataAdminBundle::ajax_layout.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('dashboard')->defaultValue('SonataAdminBundle:Core:dashboard.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('search')->defaultValue('SonataAdminBundle:Core:search.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('list')->defaultValue('RzPageBundle:BlockAdmin:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('filter')->defaultValue('SonataAdminBundle:Form:filter_admin_fields.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('show')->defaultValue('RzPageBundle:BlockAdmin:show.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('show_compare')->defaultValue('SonataAdminBundle:CRUD:show_compare.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('RzPageBundle:BlockAdmin:edit.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('preview')->defaultValue('SonataAdminBundle:CRUD:preview.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('history')->defaultValue('RzPageBundle:BlockAdmin:history.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('acl')->defaultValue('SonataAdminBundle:CRUD:acl.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('history_revision_timestamp')->defaultValue('SonataAdminBundle:CRUD:history_revision_timestamp.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('action')->defaultValue('SonataAdminBundle:CRUD:action.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('select')->defaultValue('SonataAdminBundle:CRUD:list__select.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('list_block')->defaultValue('SonataAdminBundle:Block:block_admin_list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('search_result_block')->defaultValue('SonataAdminBundle:Block:block_search_result.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('short_object_description')->defaultValue('SonataAdminBundle:Helper:short-object-description.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('delete')->defaultValue('SonataAdminBundle:CRUD:delete.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('batch')->defaultValue('SonataAdminBundle:CRUD:list__batch.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('batch_confirmation')->defaultValue('SonataAdminBundle:CRUD:batch_confirmation.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('inner_list_row')->defaultValue('SonataAdminBundle:CRUD:list_inner_row.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_mosaic')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_mosaic.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_list')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_tree')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_tree.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('base_list_field')->defaultValue('SonataAdminBundle:CRUD:base_list_field.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('pager_links')->defaultValue('SonataAdminBundle:Pager:links.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('pager_results')->defaultValue('SonataAdminBundle:Pager:results.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('tab_menu_template')->defaultValue('SonataAdminBundle:Core:tab_menu_template.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('knp_menu_template')->defaultValue('SonataAdminBundle:Menu:sonata_menu.html.twig')->cannotBeEmpty()->end()
                                        /*********************************
                                         * rzAdmin Added Templates
                                         *********************************/
                                        //** table items */
                                        ->scalarNode('rz_base_list_inner_row_header')->defaultValue('RzAdminBundle:CRUD:base_list_inner_row_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_inner_row_header')->defaultValue('RzAdminBundle:CRUD:list_inner_row_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_field_header')->defaultValue('RzAdminBundle:CRUD:base_list_field_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_field_header')->defaultValue('RzAdminBundle:CRUD:list_field_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_batch_header')->defaultValue('RzAdminBundle:CRUD:base_list_batch_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_batch_header')->defaultValue('RzAdminBundle:CRUD:list_batch_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_select_header')->defaultValue('RzAdminBundle:CRUD:base_list_select_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_select_header')->defaultValue('RzAdminBundle:CRUD:list_select_header.html.twig')->cannotBeEmpty()->end()
                                        // table actions and other components
                                        ->scalarNode('rz_list_table_footer')->defaultValue('RzAdminBundle:CRUD:list_table_footer.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_batch')->defaultValue('RzAdminBundle:CRUD:list_table_batch.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_download')->defaultValue('RzAdminBundle:CRUD:list_table_download.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_pager')->defaultValue('RzAdminBundle:CRUD:list_table_pager.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_per_page')->defaultValue('RzAdminBundle:CRUD:list_table_per_page.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\PageAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('RzPageBundle:PageAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('RzPageBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('user_block')->defaultValue('SonataAdminBundle:Core:user_block.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('add_block')->defaultValue('SonataAdminBundle:Core:add_block.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('layout')->defaultValue('SonataAdminBundle::standard_layout.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('ajax')->defaultValue('SonataAdminBundle::ajax_layout.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('dashboard')->defaultValue('SonataAdminBundle:Core:dashboard.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('search')->defaultValue('SonataAdminBundle:Core:search.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('list')->defaultValue('RzPageBundle:PageAdmin:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('filter')->defaultValue('SonataAdminBundle:Form:filter_admin_fields.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('show')->defaultValue('SonataAdminBundle:CRUD:show.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('show_compare')->defaultValue('SonataAdminBundle:CRUD:show_compare.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('RzPageBundle:PageAdmin:edit.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('preview')->defaultValue('SonataAdminBundle:CRUD:preview.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('history')->defaultValue('SonataAdminBundle:CRUD:history.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('acl')->defaultValue('SonataAdminBundle:CRUD:acl.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('history_revision_timestamp')->defaultValue('SonataAdminBundle:CRUD:history_revision_timestamp.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('action')->defaultValue('SonataAdminBundle:CRUD:action.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('select')->defaultValue('SonataAdminBundle:CRUD:list__select.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('list_block')->defaultValue('SonataAdminBundle:Block:block_admin_list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('search_result_block')->defaultValue('SonataAdminBundle:Block:block_search_result.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('short_object_description')->defaultValue('SonataAdminBundle:Helper:short-object-description.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('delete')->defaultValue('SonataAdminBundle:CRUD:delete.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('batch')->defaultValue('SonataAdminBundle:CRUD:list__batch.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('batch_confirmation')->defaultValue('SonataAdminBundle:CRUD:batch_confirmation.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('inner_list_row')->defaultValue('SonataAdminBundle:CRUD:list_inner_row.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_mosaic')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_mosaic.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_list')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_tree')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_tree.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('base_list_field')->defaultValue('SonataAdminBundle:CRUD:base_list_field.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('pager_links')->defaultValue('SonataAdminBundle:Pager:links.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('pager_results')->defaultValue('SonataAdminBundle:Pager:results.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('tab_menu_template')->defaultValue('SonataAdminBundle:Core:tab_menu_template.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('knp_menu_template')->defaultValue('SonataAdminBundle:Menu:sonata_menu.html.twig')->cannotBeEmpty()->end()
                                        /*********************************
                                         * rzAdmin Added Templates
                                         *********************************/
                                        //** table items */
                                        ->scalarNode('rz_base_list_inner_row_header')->defaultValue('RzAdminBundle:CRUD:base_list_inner_row_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_inner_row_header')->defaultValue('RzAdminBundle:CRUD:list_inner_row_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_field_header')->defaultValue('RzAdminBundle:CRUD:base_list_field_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_field_header')->defaultValue('RzAdminBundle:CRUD:list_field_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_batch_header')->defaultValue('RzAdminBundle:CRUD:base_list_batch_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_batch_header')->defaultValue('RzAdminBundle:CRUD:list_batch_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_select_header')->defaultValue('RzAdminBundle:CRUD:base_list_select_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_select_header')->defaultValue('RzAdminBundle:CRUD:list_select_header.html.twig')->cannotBeEmpty()->end()
                                        // table actions and other components
                                        ->scalarNode('rz_list_table_footer')->defaultValue('RzAdminBundle:CRUD:list_table_footer.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_batch')->defaultValue('RzAdminBundle:CRUD:list_table_batch.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_download')->defaultValue('RzAdminBundle:CRUD:list_table_download.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_pager')->defaultValue('RzAdminBundle:CRUD:list_table_pager.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_per_page')->defaultValue('RzAdminBundle:CRUD:list_table_per_page.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('site')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\SiteAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:SiteAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('RzPageBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('user_block')->defaultValue('SonataAdminBundle:Core:user_block.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('add_block')->defaultValue('SonataAdminBundle:Core:add_block.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('layout')->defaultValue('SonataAdminBundle::standard_layout.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('ajax')->defaultValue('SonataAdminBundle::ajax_layout.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('dashboard')->defaultValue('SonataAdminBundle:Core:dashboard.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('search')->defaultValue('SonataAdminBundle:Core:search.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('list')->defaultValue('SonataAdminBundle:CRUD:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('filter')->defaultValue('SonataAdminBundle:Form:filter_admin_fields.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('show')->defaultValue('SonataAdminBundle:CRUD:show.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('show_compare')->defaultValue('SonataAdminBundle:CRUD:show_compare.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('SonataAdminBundle:CRUD:edit.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('preview')->defaultValue('SonataAdminBundle:CRUD:preview.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('history')->defaultValue('SonataAdminBundle:CRUD:history.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('acl')->defaultValue('SonataAdminBundle:CRUD:acl.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('history_revision_timestamp')->defaultValue('SonataAdminBundle:CRUD:history_revision_timestamp.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('action')->defaultValue('SonataAdminBundle:CRUD:action.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('select')->defaultValue('SonataAdminBundle:CRUD:list__select.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('list_block')->defaultValue('SonataAdminBundle:Block:block_admin_list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('search_result_block')->defaultValue('SonataAdminBundle:Block:block_search_result.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('short_object_description')->defaultValue('SonataAdminBundle:Helper:short-object-description.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('delete')->defaultValue('SonataAdminBundle:CRUD:delete.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('batch')->defaultValue('SonataAdminBundle:CRUD:list__batch.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('batch_confirmation')->defaultValue('SonataAdminBundle:CRUD:batch_confirmation.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('inner_list_row')->defaultValue('SonataAdminBundle:CRUD:list_inner_row.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_mosaic')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_mosaic.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_list')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_tree')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_tree.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('base_list_field')->defaultValue('SonataAdminBundle:CRUD:base_list_field.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('pager_links')->defaultValue('SonataAdminBundle:Pager:links.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('pager_results')->defaultValue('SonataAdminBundle:Pager:results.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('tab_menu_template')->defaultValue('SonataAdminBundle:Core:tab_menu_template.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('knp_menu_template')->defaultValue('SonataAdminBundle:Menu:sonata_menu.html.twig')->cannotBeEmpty()->end()

                                        /*********************************
                                         * rzAdmin Added Templates
                                         *********************************/
                                        //** table items */
                                        ->scalarNode('rz_base_list_inner_row_header')->defaultValue('RzAdminBundle:CRUD:base_list_inner_row_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_inner_row_header')->defaultValue('RzAdminBundle:CRUD:list_inner_row_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_field_header')->defaultValue('RzAdminBundle:CRUD:base_list_field_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_field_header')->defaultValue('RzAdminBundle:CRUD:list_field_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_batch_header')->defaultValue('RzAdminBundle:CRUD:base_list_batch_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_batch_header')->defaultValue('RzAdminBundle:CRUD:list_batch_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_select_header')->defaultValue('RzAdminBundle:CRUD:base_list_select_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_select_header')->defaultValue('RzAdminBundle:CRUD:list_select_header.html.twig')->cannotBeEmpty()->end()
                                        // table actions and other components
                                        ->scalarNode('rz_list_table_footer')->defaultValue('RzAdminBundle:CRUD:list_table_footer.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_batch')->defaultValue('RzAdminBundle:CRUD:list_table_batch.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_download')->defaultValue('RzAdminBundle:CRUD:list_table_download.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_pager')->defaultValue('RzAdminBundle:CRUD:list_table_pager.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_per_page')->defaultValue('RzAdminBundle:CRUD:list_table_per_page.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('snapshot')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Admin\\SnapshotAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataPageBundle:SnapshotAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('RzPageBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('user_block')->defaultValue('SonataAdminBundle:Core:user_block.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('add_block')->defaultValue('SonataAdminBundle:Core:add_block.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('layout')->defaultValue('SonataAdminBundle::standard_layout.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('ajax')->defaultValue('SonataAdminBundle::ajax_layout.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('dashboard')->defaultValue('SonataAdminBundle:Core:dashboard.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('search')->defaultValue('SonataAdminBundle:Core:search.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('list')->defaultValue('RzPageBundle:SnapshotAdmin:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('filter')->defaultValue('SonataAdminBundle:Form:filter_admin_fields.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('show')->defaultValue('RzPageBundle:SnapshotAdmin:show.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('show_compare')->defaultValue('SonataAdminBundle:CRUD:show_compare.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('RzPageBundle:SnapshotAdmin:edit.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('preview')->defaultValue('SonataAdminBundle:CRUD:preview.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('history')->defaultValue('RzPageBundle:SnapshotAdmin:history.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('acl')->defaultValue('SonataAdminBundle:CRUD:acl.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('history_revision_timestamp')->defaultValue('SonataAdminBundle:CRUD:history_revision_timestamp.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('action')->defaultValue('SonataAdminBundle:CRUD:action.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('select')->defaultValue('SonataAdminBundle:CRUD:list__select.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('list_block')->defaultValue('SonataAdminBundle:Block:block_admin_list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('search_result_block')->defaultValue('SonataAdminBundle:Block:block_search_result.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('short_object_description')->defaultValue('SonataAdminBundle:Helper:short-object-description.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('delete')->defaultValue('SonataAdminBundle:CRUD:delete.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('batch')->defaultValue('SonataAdminBundle:CRUD:list__batch.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('batch_confirmation')->defaultValue('SonataAdminBundle:CRUD:batch_confirmation.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('inner_list_row')->defaultValue('SonataAdminBundle:CRUD:list_inner_row.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_mosaic')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_mosaic.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_list')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('outer_list_rows_tree')->defaultValue('SonataAdminBundle:CRUD:list_outer_rows_tree.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('base_list_field')->defaultValue('SonataAdminBundle:CRUD:base_list_field.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('pager_links')->defaultValue('SonataAdminBundle:Pager:links.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('pager_results')->defaultValue('SonataAdminBundle:Pager:results.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('tab_menu_template')->defaultValue('SonataAdminBundle:Core:tab_menu_template.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('knp_menu_template')->defaultValue('SonataAdminBundle:Menu:sonata_menu.html.twig')->cannotBeEmpty()->end()
                                        /*********************************
                                         * rzAdmin Added Templates
                                         *********************************/
                                        //** table items */
                                        ->scalarNode('rz_base_list_inner_row_header')->defaultValue('RzAdminBundle:CRUD:base_list_inner_row_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_inner_row_header')->defaultValue('RzAdminBundle:CRUD:list_inner_row_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_field_header')->defaultValue('RzAdminBundle:CRUD:base_list_field_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_field_header')->defaultValue('RzAdminBundle:CRUD:list_field_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_batch_header')->defaultValue('RzAdminBundle:CRUD:base_list_batch_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_batch_header')->defaultValue('RzAdminBundle:CRUD:list_batch_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_base_list_select_header')->defaultValue('RzAdminBundle:CRUD:base_list_select_header.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_select_header')->defaultValue('RzAdminBundle:CRUD:list_select_header.html.twig')->cannotBeEmpty()->end()
                                        // table actions and other components
                                        ->scalarNode('rz_list_table_footer')->defaultValue('RzAdminBundle:CRUD:list_table_footer.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_batch')->defaultValue('RzAdminBundle:CRUD:list_table_batch.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_download')->defaultValue('RzAdminBundle:CRUD:list_table_download.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_pager')->defaultValue('RzAdminBundle:CRUD:list_table_pager.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('rz_list_table_per_page')->defaultValue('RzAdminBundle:CRUD:list_table_per_page.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addBlockSettings(ArrayNodeDefinition $node) {
        $node
            ->children()
                ->arrayNode('blocks')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('container')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Block\\ContainerBlockService')->end()
                            ->end()
                        ->end()
                        ->arrayNode('children_pages')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Block\\ChildrenPagesBlockService')->end()
                            ->end()
                        ->end()
                        ->arrayNode('shared_block')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\PageBundle\\Block\\SharedBlockBlockService')->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end();
    }
}
