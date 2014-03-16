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

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\PageBundle\Model\PageInterface;

use Sonata\PageBundle\Admin\PageAdmin as BasePageAdmin;

/**
 * Admin definition for the Page class
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class PageAdmin extends BasePageAdmin
{

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('site')
            ->add('routeName')
            ->add('pageAlias')
            ->add('type')
            ->add('enabled')
            ->add('decorate')
            ->add('name')
            ->add('slug', 'text')
            ->add('customUrl', 'text')
            ->add('edited')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('hybrid', 'text', array('template' => 'SonataPageBundle:PageAdmin:field_hybrid.html.twig',
                                          'footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))

            ->addIdentifier('name', null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('site', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('decorate', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone'))))
            ->add('edited', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->getSubject() || (!$this->getSubject()->isInternal() && !$this->getSubject()->isError())) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                    ->add('url', 'text', array('attr' => array('readonly' => 'readonly')))
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('form_page.group_main_label'))
                ->add('parent', 'sonata_page_selector', array(
                              'page'          => $this->getSubject() ?: null,
                              'site'          => $this->getSubject() ? $this->getSubject()->getSite() : null,
                              'model_manager' => $this->getModelManager(),
                              'class'         => $this->getClass(),
                              'required'      => false,
                              'select2'=>true,
                          ))
            ->end();

        if (!$this->getSubject() || !$this->getSubject()->isDynamic()) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                ->add('target', 'sonata_page_selector', array(
                                  'page'          => $this->getSubject() ?: null,
                                  'site'          => $this->getSubject() ? $this->getSubject()->getSite() : null,
                                  'model_manager' => $this->getModelManager(),
                                  'class'         => $this->getClass(),
                                  'filter_choice' => array('request_method' => 'all'),
                                  'required'      => false,
                                  'select2'=>true,
                              ))
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('form_page.group_main_label'))
            ->add('site', null, array('required' => true, 'select2' => true))
            ->add('name')
            ->add('enabled', null, array('required' => false))
            ->add('position')
            ->end();

        if ($this->hasSubject() && !$this->getSubject()->isInternal()) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                    ->add('type', 'sonata_page_type_choice', array('required' => false, 'select2' => true))
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('form_page.group_main_label'))
                ->add('templateCode', 'sonata_page_template', array('required' => true, 'select2' => true))
            ->end()
        ;

        if (!$this->getSubject() || !$this->getSubject()->isDynamic()) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                    ->add('pageAlias', null, array('required' => false))
            ;
        }

        if (!$this->getSubject() || !$this->getSubject()->isHybrid()) {
            $formMapper
                ->with($this->trans('form_page.group_seo_label'))
                    ->add('slug', 'text',  array('required' => false))
                    ->add('customUrl', 'text', array('required' => false))
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('form_page.group_seo_label'), array('collapsed' => true))
                ->add('title', null, array('required' => false))
                ->add('metaKeyword', 'textarea', array('required' => false, 'attr'=>array('rows'=>5)))
                ->add('metaDescription', 'textarea', array('required' => false, 'attr'=>array('rows'=>5)))
            ->end()
        ;

        if ($this->hasSubject() && !$this->getSubject()->isCms()) {
            $formMapper
                ->with($this->trans('form_page.group_advanced_label'), array('collapsed' => true))
                    ->add('decorate', null,  array('required' => false))
                ->end();
        }

        $formMapper
            ->with($this->trans('form_page.group_advanced_label'), array('collapsed' => true))
                ->add('javascript', 'rz_codemirror',  array('required' => false))
                ->add('stylesheet', 'rz_codemirror', array('required' => false))
                ->add('rawHeaders', 'rz_codemirror', array('required' => false))
            ->end()
        ;

        $formMapper->setHelps(array(
            'name' => $this->trans('help_page_name')
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('site')
            ->add('name')
            ->add('type', null, array('field_type' => 'sonata_page_type_choice'))
            ->add('pageAlias')
            ->add('parent')
            ->add('edited')
            ->add('hybrid', 'doctrine_orm_callback', array(
                              'callback' => function($queryBuilder, $alias, $field, $data) {
                                  if (in_array($data['value'], array('hybrid', 'cms'))) {
                                      $queryBuilder->andWhere(sprintf('%s.routeName %s :routeName', $alias, $data['value'] == 'cms' ? '=' : '!='));
                                      $queryBuilder->setParameter('routeName', PageInterface::PAGE_ROUTE_CMS_NAME);
                                  }
                              },
                              'field_options' => array(
                                  'required' => false,
                                  'choices'  => array(
                                      'hybrid'  => $this->trans('hybrid'),
                                      'cms'     => $this->trans('cms'),
                                  ),
                                  'selectpicker_dropup' => true,
                              ),
                              'field_type' => 'choice'
                          ))
        ;
    }
}
