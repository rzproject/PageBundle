<?php

/*
 * This file is part of the RzPageBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\PageBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
//        $definition = $container->getDefinition('sonata.page.admin.page');
//        $definition->setClass($container->getParameter('rz_page.admin.page.class'));
//
//        $definition = $container->getDefinition('sonata.page.admin.block');
//        $definition->setClass($container->getParameter('rz_page.admin.block.class'));
//
//        $definition = $container->getDefinition('sonata.page.admin.snapshot');
//        $definition->setClass($container->getParameter('rz_page.admin.snapshot.class'));
//
//        $definition = $container->getDefinition('sonata.page.admin.site');
//        $definition->setClass($container->getParameter('rz_page.admin.site.class'));

        $definition = $container->getDefinition('sonata.page.block.container');
        $definition->setClass($container->getParameter('rz_page.block.container.class'));

        $definition = $container->getDefinition('sonata.page.block.children_pages');
        $definition->setClass($container->getParameter('rz_page.block.children_pages.class'));

        /* TODO: deprecated */
//        $definition = $container->getDefinition('sonata.page.response_listener');
//        $definition->setClass($container->getParameter('rz_page.response_listener.class'));

        //Override Templates

        $definition = $container->getDefinition('sonata.page.admin.site');
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_page.configuration.site.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        $definition = $container->getDefinition('sonata.page.admin.block');
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_page.configuration.block.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        $definition = $container->getDefinition('sonata.page.admin.snapshot');
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_page.configuration.snapshot.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        $definition = $container->getDefinition('sonata.page.admin.page');
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_page.configuration.page.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

    }
}
