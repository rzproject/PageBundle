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
        //$rz_definition = $container->getDefinition('rz_user.admin.user');

        //override User Admin
        $definition = $container->getDefinition('sonata.page.admin.page');
        $definition->setClass($container->getParameter('rz_page.admin.page.class'));

        $definition = $container->getDefinition('sonata.page.admin.block');
        $definition->setClass($container->getParameter('rz_page.admin.block.class'));

        $definition = $container->getDefinition('sonata.page.admin.site');
        $definition->setClass($container->getParameter('rz_page.admin.site.class'));

        $definition = $container->getDefinition('sonata.page.block.container');
        $definition->setClass($container->getParameter('rz_page.block.container.class'));

        $definition = $container->getDefinition('sonata.page.block.children_pages');
        $definition->setClass($container->getParameter('rz_page.block.children_pages.class'));

    }
}
