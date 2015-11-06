<?php

namespace Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\SiteAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Admin definition for the Site class.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SiteAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('isDefault')
            ->add('enabled')
            ->add('host')
            ->add('locale')
            ->add('relativePath')
            ->add('enabledFrom')
            ->add('enabledTo')
            ->add('title')
            ->add('metaDescription')
            ->add('metaKeywords')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('isDefault', null, array('editable' => true,'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('enabled', null, array('editable' => true,'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('host', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('relativePath', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('locale', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('enabledFrom', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('enabledTo', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('create_snapshots', 'string', array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm'))),'template' => 'RzPageBundle:SiteAdmin:list_create_snapshots.html.twig'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_site.label_general', array('class' => 'col-md-6'))
                ->add('name')
                ->add('isDefault', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('host')
                ->add('locale', 'locale', array(
                    'required' => false,
                ))
                ->add('relativePath', null, array('required' => false))
                ->add('enabledFrom', 'sonata_type_datetime_picker', array('dp_side_by_side' => true))
                ->add('enabledTo', 'sonata_type_datetime_picker', array(
                    'required'        => false,
                    'dp_side_by_side' => true,
                ))
            ->end()
            ->with('form_site.label_seo', array('class' => 'col-md-6'))
                ->add('title', null, array('required' => false))
                ->add('metaDescription', 'textarea', array('required' => false))
                ->add('metaKeywords', 'textarea', array('required' => false))
            ->end()
        ;
    }
}
