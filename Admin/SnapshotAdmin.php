<?php

/*
 * This file is part of the RzPageBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\SnapshotAdmin as BaseSnapshotAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class SnapshotAdmin extends BaseSnapshotAdmin
{

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('url', null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('enabled', null , array('footable'=>array('attr'=>array('data_hide'=>'tablet'))))
            ->add('publicationDateStart', null , array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('publicationDateEnd', null , array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('_action', 'actions', array(
                             'actions' => array(
                                'Show' => array('template' => 'SonataAdminBundle:CRUD:list__action_show.html.twig'),
                                'Edit' => array('template' => 'SonataAdminBundle:CRUD:list__action_edit.html.twig'),
                                'Delete' => array('template' => 'SonataAdminBundle:CRUD:list__action_delete.html.twig')),
                             'footable'=>array('attr'=>array('data_hide'=>'phone,tablet')),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('url')
            ->add('enabled')
            ->add('publicationDateStart')
            ->add('publicationDateEnd')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('publicationDateStart')
            ->add('publicationDateEnd')
        ;
    }
}
