<?php

namespace Rz\PageBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        #####################################
        ## Override Entity Manager
        #####################################
        $definition = $container->getDefinition('sonata.page.manager.site');
        $definition->setClass($container->getParameter('rz.page.entity.manager.site.class'));

        $definition = $container->getDefinition('sonata.page.manager.snapshot');
        $definition->setClass($container->getParameter('rz.page.entity.manager.snapshot.class'));
        $definition->addMethodCall('setRedirectManager', array(new Reference('rz.redirect.manager.redirect')));


        $definition = $container->getDefinition('sonata.page.manager.page');
        $definition->setClass($container->getParameter('rz.page.entity.manager.page.class'));

        $definition = $container->getDefinition('sonata.page.manager.block');
        $definition->setClass($container->getParameter('rz.page.entity.manager.block.class'));


        #####################################
        ## Override Site Admin
        #####################################
        $definition = $container->getDefinition('sonata.page.admin.site');
        $definition->setClass($container->getParameter('rz.page.admin.site.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('rz.page.admin.site.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('rz.page.admin.site.controller')));

        #####################################
        ## Override Snapshot Admin
        #####################################
        $definition = $container->getDefinition('sonata.page.admin.snapshot');
        $definition->setClass($container->getParameter('rz.page.admin.snapshot.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('rz.page.admin.snapshot.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('rz.page.admin.snapshot.controller')));

        #####################################
        ## Override Page Admin
        #####################################
        $definition = $container->getDefinition('sonata.page.admin.page');
        $definition->setClass($container->getParameter('rz.page.admin.page.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('rz.page.admin.page.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('rz.page.admin.page.controller')));

        #####################################
        ## Override Block Admin
        #####################################
        $definition = $container->getDefinition('sonata.page.admin.block');
        $definition->setClass($container->getParameter('rz.page.admin.block.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('rz.page.admin.block.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('rz.page.admin.block.controller')));

        #####################################
        ## Override Shared Admin
        #####################################
        $definition = $container->getDefinition('sonata.page.admin.shared_block');
        //override
        $container->setParameter('sonata.page.admin.shared_block.class', $container->getParameter('rz.page.admin.shared_block.class'));
        $definition->setClass($container->getParameter('rz.page.admin.shared_block.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('rz.page.admin.shared_block.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('rz.page.admin.shared_block.controller')));
        $definition->addMethodCall('setBlocks', array($container->getParameter('sonata_block.blocks')));


        #####################################
        ## Override Block
        #####################################
        $definition = $container->getDefinition('sonata.page.block.container');
        $definition->setClass($container->getParameter('rz.page.block.container.class'));

        $definition = $container->getDefinition('sonata.page.block.children_pages');
        $definition->setClass($container->getParameter('rz.page.block.children_pages.class'));

        $definition = $container->getDefinition('sonata.page.block.breadcrumb');
        $definition->setClass($container->getParameter('rz.page.block.breadcrumb.class'));

        $definition = $container->getDefinition('sonata.page.block.shared_block');
        $definition->setClass($container->getParameter('rz.page.block.shared_block.class'));

        $definition = $container->getDefinition('sonata.page.block.pagelist');
        $definition->setClass($container->getParameter('rz.page.block.pagelist.class'));


        #####################################
        ## Consumer Class
        #####################################
        if($container->hasParameter('rz.page.consumer.create_snapshots.class')) {
            $definition = $container->getDefinition('sonata.page.notification.create_snapshots');
            $definition->setClass($container->getParameter('rz.page.consumer.create_snapshots.class'));
        }

        if($container->hasParameter('rz.page.consumer.create_snapshot.class')) {
            $definition = $container->getDefinition('sonata.page.notification.create_snapshot');
            $definition->setClass($container->getParameter('rz.page.consumer.create_snapshot.class'));
        }

        if($container->hasParameter('rz.page.consumer.cleanup_snapshots.class')) {
            $definition = $container->getDefinition('sonata.page.notification.cleanup_snapshots');
            $definition->setClass($container->getParameter('rz.page.consumer.cleanup_snapshots.class'));
        }

        if($container->hasParameter('rz.page.consumer.cleanup_snapshot.class')) {
            $definition = $container->getDefinition('sonata.page.notification.cleanup_snapshot');
            $definition->setClass($container->getParameter('rz.page.consumer.cleanup_snapshot.class'));
        }
    }
}
