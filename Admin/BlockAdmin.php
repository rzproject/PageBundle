<?php

namespace Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\BlockAdmin as Admin;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * Admin class for the Block model.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BlockAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('type')
            ->add('name', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('enabled', null, array('editable' => true,'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('updatedAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('position', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
        ;
    }
}
