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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RzPageExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('twig.xml');

        $this->configureAdminClass($config, $container);
        $this->configureClass($config, $container);
        $this->configureClassManager($config, $container);

        $this->configureTranslationDomain($config, $container);
        $this->configureController($config, $container);
        $this->configureRzTemplates($config, $container);
        $this->configureBlocks($config['blocks'], $container);
        $this->registerDoctrineMapping($config);
    }

    /**
     * @param array  $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.page.site.class', $config['class']['site']);
        $container->setParameter('sonata.page.block.class', $config['class']['block']);
        $container->setParameter('sonata.page.snapshot.class', $config['class']['snapshot']);
        $container->setParameter('sonata.page.page.class', $config['class']['page']);
        $container->setParameter('rz.page.service.default.class', $config['class']['page_service_default']);
        $container->setParameter('rz.page.transformer.class', $config['class']['page_transformer']);


        $container->setParameter('sonata.page.admin.site.entity', $config['class']['site']);
        $container->setParameter('sonata.page.admin.block.entity', $config['class']['block']);
        $container->setParameter('sonata.page.admin.snapshot.entity', $config['class']['snapshot']);
        $container->setParameter('sonata.page.admin.page.entity', $config['class']['page']);
    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureAdminClass($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.page.admin.site.class', $config['admin']['site']['class']);
        $container->setParameter('sonata.page.admin.block.class', $config['admin']['block']['class']);
        $container->setParameter('sonata.page.admin.snapshot.class', $config['admin']['snapshot']['class']);
        $container->setParameter('sonata.page.admin.page.class', $config['admin']['page']['class']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureClassManager($config, ContainerBuilder $container)
    {
        // manager configuration
        $container->setParameter('sonata.page.manager.site.class', $config['class_manager']['site']);
        $container->setParameter('sonata.page.manager.block.class', $config['class_manager']['block']);
        $container->setParameter('sonata.page.manager.snapshot.class', $config['class_manager']['snapshot']);
        $container->setParameter('sonata.page.manager.page.class', $config['class_manager']['page']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureTranslationDomain($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.page.admin.site.translation_domain', $config['admin']['site']['translation']);
        $container->setParameter('sonata.page.admin.block.translation_domain', $config['admin']['block']['translation']);
        $container->setParameter('sonata.page.admin.snapshot.translation_domain', $config['admin']['snapshot']['translation']);
        $container->setParameter('sonata.page.admin.page.translation_domain', $config['admin']['page']['translation']);
    }


    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureController($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.page.admin.site.controller', $config['admin']['site']['controller']);
        $container->setParameter('sonata.page.admin.block.controller', $config['admin']['block']['controller']);
        $container->setParameter('sonata.page.admin.snapshot.controller', $config['admin']['snapshot']['controller']);
        $container->setParameter('sonata.page.admin.page.controller', $config['admin']['page']['controller']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureRzTemplates($config, ContainerBuilder $container)
    {
        $container->setParameter('rz_page.configuration.site.templates', $config['admin']['site']['templates']);
        $container->setParameter('rz_page.configuration.block.templates', $config['admin']['block']['templates']);
        $container->setParameter('rz_page.configuration.snapshot.templates', $config['admin']['snapshot']['templates']);
        $container->setParameter('rz_page.configuration.page.templates', $config['admin']['page']['templates']);
    }

    public function configureBlocks($config, ContainerBuilder $container)
    {
        $container->setParameter('rz_page.block.container.class', $config['container']['class']);
        $container->setParameter('rz_page.block.children_pages.class', $config['children_pages']['class']);
        $container->setParameter('rz_page.block.shared_block.class', $config['shared_block']['class']);
    }

    /**
     * Registers doctrine mapping on concrete page entities
     *
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['page'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $collector->addAssociation($config['class']['page'], 'mapManyToOne', array(
                'fieldName' => 'ogImage',
                'targetEntity' => $config['class']['media'],
                'cascade' =>
                    array(
                        0 => 'persist',
                        1 => 'detach',
                    ),
                'mappedBy' => NULL,
                'inversedBy' => NULL,
                'joinColumns' =>
                    array(
                        array(
                            'name' => 'og_image_id',
                            'referencedColumnName' => 'id',
                        ),
                    ),
                'orphanRemoval' => false,
            ));
        }


    }

}
