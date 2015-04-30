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
use Symfony\Component\DependencyInjection\Reference;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        #################
        # Site Admin
        #################
        $definition = $container->getDefinition('sonata.page.admin.site');
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_page.configuration.site.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        #################
        # Block Admin
        #################
        $definition = $container->getDefinition('sonata.page.admin.block');
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_page.configuration.block.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        #################
        # Snapshot Admin
        #################
        $definition = $container->getDefinition('sonata.page.admin.snapshot');
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_page.configuration.snapshot.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        #################
        # Page Admin
        #################
        $definition = $container->getDefinition('sonata.page.admin.page');
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_page.configuration.page.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));
        $definition->addMethodCall('setMetaTags', array($container->getParameter('rz_seo.metatags')));

        #################
        # Page Service
        #################
        $definition = $container->getDefinition('sonata.page.service.default');
        $definition->setClass($container->getParameter('rz.page.service.default.class'));
        $definition->addMethodCall('setRouter', array(new Reference('router')));

        #################
        # Page Transformer
        #################
        $definition = $container->getDefinition('sonata.page.transformer');
        $definition->setClass($container->getParameter('rz.page.transformer.class'));

        #################
        # Shared Block
        #################
        $definition = $container->getDefinition('sonata.page.block.shared_block');
        $definition->setClass($container->getParameter('rz_page.block.shared_block.class'));

        #################
        # Container Block
        #################
        $definition = $container->getDefinition('sonata.page.block.container');
        $definition->setClass($container->getParameter('rz_page.block.container.class'));

        #################
        # Children Pages Block
        #################
        $definition = $container->getDefinition('sonata.page.block.children_pages');
        $definition->setClass($container->getParameter('rz_page.block.children_pages.class'));

    }
}
