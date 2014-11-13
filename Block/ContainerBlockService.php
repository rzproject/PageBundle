<?php

/*
 * This file is part of the RzPageBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\PageBundle\Block;
use Sonata\PageBundle\Block\ContainerBlockService as BaseContainerBlockService;

use Sonata\AdminBundle\Form\FormMapper;

use Sonata\BlockBundle\Model\BlockInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sonata\PageBundle\Model\SnapshotPageProxy;

/**
 * Render children pages
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ContainerBlockService extends BaseContainerBlockService
{
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('enabled');

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('code', 'text', array('required' => false, 'attr'=>array('class'=>'span8'))),
                array('layout', 'ckeditor', array()),
                array('class', 'text', array('required' => false, 'attr'=>array('class'=>'span8'))),
                array('template', 'sonata_type_container_template_choice', array('attr'=>array('class'=>'span8')))
            )
        ));

        $formMapper->add('children', 'sonata_type_collection', array(), array(
            'edit'   => 'inline',
            'inline' => 'table',
            'sortable' => 'position'
        ));
    }



    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'code'        => '',
            'layout'      => '{{ CONTENT }}',
            'class'       => '',
            'template'    => 'SonataPageBundle:Block:block_container.html.twig',
        ));
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function getCacheKeys(BlockInterface $block)
//    {
//        return array(
//            'block_id'   => $block->getId(),
//            'page_id'    => $block->getPage()->getId(),
//            'manager'    => $block->getPage() instanceof SnapshotPageProxy ? 'snapshot' : 'page',
//            'updated_at' => $block->getUpdatedAt()->format('U'),
//        );
//    }
}
