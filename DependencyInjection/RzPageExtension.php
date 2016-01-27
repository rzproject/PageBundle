<?php

namespace Rz\PageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RzPageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $this->configureManagerClass($config, $container);
        $this->configureAdminClass($config, $container);
        $loader->load('twig.xml');
    }

    public function configureManagerClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.page.entity.manager.site.class',           $config['manager_class']['orm']['site']);
        $container->setParameter('rz.page.entity.manager.snapshot.class',       $config['manager_class']['orm']['snapshot']);
        $container->setParameter('rz.page.entity.manager.page.class',           $config['manager_class']['orm']['page']);
        $container->setParameter('rz.page.entity.manager.block.class',          $config['manager_class']['orm']['block']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureAdminClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.page.admin.site.class',                  $config['admin']['site']['class']);
        $container->setParameter('rz.page.admin.site.controller',             $config['admin']['site']['controller']);
        $container->setParameter('rz.page.admin.site.translation_domain',     $config['admin']['site']['translation']);

        $container->setParameter('rz.page.admin.snapshot.class',              $config['admin']['snapshot']['class']);
        $container->setParameter('rz.page.admin.snapshot.controller',         $config['admin']['snapshot']['controller']);
        $container->setParameter('rz.page.admin.snapshot.translation_domain', $config['admin']['snapshot']['translation']);

        $container->setParameter('rz.page.admin.page.class',                  $config['admin']['page']['class']);
        $container->setParameter('rz.page.admin.page.controller',             $config['admin']['page']['controller']);
        $container->setParameter('rz.page.admin.page.translation_domain',     $config['admin']['page']['translation']);

        $container->setParameter('rz.page.admin.block.class',                 $config['admin']['block']['class']);
        $container->setParameter('rz.page.admin.block.controller',            $config['admin']['block']['controller']);
        $container->setParameter('rz.page.admin.block.translation_domain',    $config['admin']['block']['translation']);

        $container->setParameter('rz.page.admin.shared_block.class',                 $config['admin']['shared_block']['class']);
        $container->setParameter('rz.page.admin.shared_block.controller',            $config['admin']['shared_block']['controller']);
        $container->setParameter('rz.page.admin.shared_block.translation_domain',    $config['admin']['shared_block']['translation']);
    }
}
