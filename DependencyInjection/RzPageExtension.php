<?php

namespace Rz\PageBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
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
        $this->configureBlockClass($config, $container);
        $this->configureConsumerClass($config, $container);
        $this->registerDoctrineMapping($config, $container);
        $loader->load('twig.xml');
    }

    public function configureManagerClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.page.entity.manager.site.class',           $config['manager_class']['orm']['site']);
        $container->setParameter('rz.page.entity.manager.snapshot.class',       $config['manager_class']['orm']['snapshot']);
        $container->setParameter('rz.page.entity.manager.page.class',           $config['manager_class']['orm']['page']);
        $container->setParameter('rz.page.entity.manager.block.class',          $config['manager_class']['orm']['block']);
    }

    public function configureBlockClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.page.block.container.class',          $config['block']['container']);
        $container->setParameter('rz.page.block.children_pages.class',     $config['block']['children_pages']);
        $container->setParameter('rz.page.block.breadcrumb.class',         $config['block']['breadcrumb']);
        $container->setParameter('rz.page.block.shared_block.class',       $config['block']['shared_block']);
        $container->setParameter('rz.page.block.pagelist.class',           $config['block']['pagelist']);
    }


    public function configureConsumerClass($config, ContainerBuilder $container)
    {
        if (isset($config['consumer_class']['create_snapshots'])) {
            $container->setParameter('rz.page.consumer.create_snapshots.class', $config['consumer_class']['create_snapshots']);
        }

        if (isset($config['consumer_class']['create_snapshot'])) {
            $container->setParameter('rz.page.consumer.create_snapshot.class', $config['consumer_class']['create_snapshot']);
        }

        if (isset($config['consumer_class']['cleanup_snapshots'])) {
            $container->setParameter('rz.page.consumer.cleanup_snapshots.class', $config['consumer_class']['cleanup_snapshots']);
        }

        if (isset($config['consumer_class']['cleanup_snapshot'])) {
            $container->setParameter('rz.page.consumer.cleanup_snapshot.class', $config['consumer_class']['cleanup_snapshot']);
        }
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

    /**
     * Registers doctrine mapping on concrete page entities.
     *
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['page'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['page'], 'mapManyToOne', array(
            'fieldName'     => 'canonicalPage',
            'targetEntity'  => $config['class']['page'],
            'cascade'       => array(
                'persist',
            ),
            'mappedBy'      => null,
            'inversedBy'    => null,
            'joinColumns'   => array(
                array(
                    'name'                 => 'canonical_page_id',
                    'referencedColumnName' => 'id',
                    'onDelete'             => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));
    }
}
