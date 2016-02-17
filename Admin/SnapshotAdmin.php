<?php

namespace Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\SnapshotAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Cache\CacheManagerInterface;
use Sonata\CoreBundle\Model\ManagerInterface;

/**
 * Admin definition for the Snapshot class.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SnapshotAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('url')
            ->add('enabled', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('publicationDateStart', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('publicationDateEnd', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('routeName')
            ->add('publicationDateStart', 'doctrine_orm_datetime_range', array('field_type' => 'sonata_type_datetime_range_picker'))
            ->add('publicationDateEnd', 'doctrine_orm_datetime_range', array('field_type' => 'sonata_type_datetime_range_picker'))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('publicationDateStart', 'sonata_type_datetime_picker', array('dp_side_by_side' => true))
            ->add('publicationDateEnd', 'sonata_type_datetime_picker', array('required' => false, 'dp_side_by_side' => true))
        ;
    }
}
