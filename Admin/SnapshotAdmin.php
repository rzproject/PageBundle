<?php

namespace  Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\SnapshotAdmin as BaseSnapshotAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\CacheBundle\Cache\CacheManagerInterface;

/**
 * Admin definition for the Snapshot class
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SnapshotAdmin extends BaseSnapshotAdmin
{


    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('publicationDateStart')
            ->add('publicationDateEnd')
//            ->add('content')
        ;
    }
}
