<?php

namespace  Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\SiteAdmin as BaseSiteAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * Admin definition for the Site class
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SiteAdmin extends BaseSiteAdmin
{

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('isDefault', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone'))))
            ->add('host', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('relativePath', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabledFrom', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabledTo', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('create_snapshots', 'string', array('template' => 'SonataPageBundle:SiteAdmin:list_create_snapshots.html.twig',
                                                      'footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('form_site.label_general'))
                ->add('name')
                ->add('isDefault', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('host')
                ->add('locale', null, array(
                    'required' => false
                ))
                ->add('relativePath', null, array('required' => false))
                ->add('enabledFrom')
                ->add('enabledTo')
            ->end()
            ->with($this->trans('form_site.label_seo'))
                ->add('title', null, array('required' => false))
                ->add('metaDescription', 'textarea', array('required' => false, 'attr'=>array('rows'=>5)))
                ->add('metaKeywords', 'textarea', array('required' => false, 'attr'=>array('rows'=>5)))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null ,array('operator_options'=> array('expanded' => true)))
        ;
    }

}
