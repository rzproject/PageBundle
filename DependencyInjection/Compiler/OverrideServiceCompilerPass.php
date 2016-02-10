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
        ## Add Redirect Manager
        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['RzRedirectBundle'])) {
            $definition->addMethodCall('setRedirectManager', array(new Reference('rz.redirect.manager.redirect')));
        }

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
        $definition->setClass($container->getParameter('rz.page.admin.shared_block.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('rz.page.admin.shared_block.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('rz.page.admin.shared_block.controller')));
    }
}
