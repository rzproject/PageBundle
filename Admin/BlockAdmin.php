<?php

namespace  Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\BlockAdmin as BaseBlockAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * Admin class for the Block model
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BlockAdmin extends BaseBlockAdmin
{

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('type', null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('name', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabled', null, array('footable'=>array('attr'=>array('data_hide'=>'phone'))))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $block = $this->getSubject();
        // add name on all forms
        $formMapper->add('name', null, array('attr'=>array('class'=>"span12")));

        $isContainerRoot = $block && $block->getType() == 'sonata.page.block.container' && !$this->hasParentFieldDescription();
        $isStandardBlock = $block && $block->getType() != 'sonata.page.block.container' && !$this->hasParentFieldDescription();

        if ($isContainerRoot || $isStandardBlock) {
            $service = $this->blockManager->get($block);

            if ($block->getId() > 0) {
                $service->buildEditForm($formMapper, $block);
            } else {
                $service->buildCreateForm($formMapper, $block);
            }
        } else {
            $formMapper
                ->add('type', 'sonata_block_service_choice', array('context' => 'cms'))
                ->add('enabled')
                ->add('position');
        }
    }
}
