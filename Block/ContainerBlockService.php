<?php

namespace Rz\PageBundle\Block;
use Sonata\PageBundle\Block\ContainerBlockService as BaseContainerBlockService;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
                array('layout', 'rz_codemirror', array()),
                array('class', 'text', array('required' => false, 'attr'=>array('class'=>'span12'))),
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
}
