<?php

namespace Rz\PageBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\PageBundle\Admin\PageAdmin as Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\PageBundle\Model\PageInterface;

/**
 * Admin definition for the Page class.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class PageAdmin extends Admin
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
            ->add('slug')
            ->add('customUrl')
            ->add('edited')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('hybrid', 'text', array('template' => 'RzPageBundle:PageAdmin:field_hybrid.html.twig'))
            ->addIdentifier('name')
            ->add('type', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('pageAlias', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('site', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('decorate', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('edited', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
        ;
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
                'callback' => function ($queryBuilder, $alias, $field, $data) {
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
                ),
                'field_type' => 'choice',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        // define group zoning
        $formMapper
             ->with($this->trans('form_page.group_main_label'), array('class' => 'col-md-6'))->end()
             ->with('form_page.group_seo_label', array('class' => 'col-md-6'))->end()
             ->with('form_page.group_advanced_label', array('class' => 'col-md-12'))->end()
        ;

        if (!$this->getSubject() || (!$this->getSubject()->isInternal() && !$this->getSubject()->isError())) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                    ->add('url', 'text', array('attr' => array('readonly' => 'readonly')))
                ->end()
            ;
        }

        if ($this->hasSubject() && !$this->getSubject()->getId()) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                    ->add('site', null, array('required' => true, 'read_only' => true))
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('form_page.group_main_label'))
                ->add('name')
                ->add('enabled', null, array('required' => false))
                ->add('position')
            ->end()
        ;

        if ($this->hasSubject() && !$this->getSubject()->isInternal()) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                    ->add('type', 'sonata_page_type_choice', array('required' => false))
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('form_page.group_main_label'))
                ->add('templateCode', 'sonata_page_template', array('required' => true))
            ->end()
        ;

        if (!$this->getSubject() || ($this->getSubject() && $this->getSubject()->getParent()) || ($this->getSubject() && !$this->getSubject()->getId())) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                    ->add('parent', 'sonata_page_selector', array(
                        'page'          => $this->getSubject() ?: null,
                        'site'          => $this->getSubject() ? $this->getSubject()->getSite() : null,
                        'model_manager' => $this->getModelManager(),
                        'class'         => $this->getClass(),
                        'required'      => false,
                        'filter_choice' => array('hierarchy' => 'root'),
                    ), array(
                        'admin_code'      => $this->getCode(),
                        'link_parameters' => array(
                            'siteId' => $this->getSubject() ? $this->getSubject()->getSite()->getId() : null,
                        ),
                    ))
                ->end()
            ;
        }

        if (!$this->getSubject() || !$this->getSubject()->isDynamic()) {
            $formMapper
                ->with($this->trans('form_page.group_main_label'))
                    ->add('pageAlias', null, array('required' => false))
                    ->add('target', 'sonata_page_selector', array(
                        'page'          => $this->getSubject() ?: null,
                        'site'          => $this->getSubject() ? $this->getSubject()->getSite() : null,
                        'model_manager' => $this->getModelManager(),
                        'class'         => $this->getClass(),
                        'filter_choice' => array('request_method' => 'all'),
                        'required'      => false,
                    ), array(
                        'admin_code'      => $this->getCode(),
                        'link_parameters' => array(
                            'siteId' => $this->getSubject() ? $this->getSubject()->getSite()->getId() : null,
                        ),
                    ))
                ->end()
            ;
        }

        if (!$this->getSubject() || !$this->getSubject()->isHybrid()) {
            $formMapper
                ->with('form_page.group_seo_label')
                    ->add('slug', 'text',  array('required' => false))
                    ->add('customUrl', 'text', array('required' => false))
                ->end()
            ;
        }

        $formMapper
            ->with('form_page.group_seo_label', array('collapsed' => true))
                ->add('title', null, array('required' => false))
                ->add('metaKeyword', 'textarea', array('required' => false))
                ->add('metaDescription', 'textarea', array('required' => false))
                ->add('canonicalPage', 'sonata_page_selector', array(
                        'page'          => $this->getSubject() ?: null,
                        'site'          => $this->getSubject() ? $this->getSubject()->getSite() : null,
                        'model_manager' => $this->getModelManager(),
                        'class'         => $this->getClass(),
                        'required'      => false,
                        'filter_choice' => array('request_method' => 'all'),
                    ), array(
                        'admin_code'      => $this->getCode(),
                        'link_parameters' => array(
                            'siteId' => $this->getSubject() ? $this->getSubject()->getSite()->getId() : null,
                        ),
                    ))
            ->end()
        ;

        if ($this->hasSubject() && !$this->getSubject()->isCms()) {
            $formMapper
                ->with('form_page.group_advanced_label', array('collapsed' => true))
                    ->add('decorate', null,  array('required' => false))
                ->end()
            ;
        }

        $formMapper
            ->with('form_page.group_advanced_label', array('collapsed' => true))
                ->add('javascript', null,  array('required' => false))
                ->add('stylesheet', null, array('required' => false))
                ->add('rawHeaders', null, array('required' => false))
            ->end()
        ;

        $formMapper->setHelps(array(
            'name' => $this->trans('help_page_name'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_edit_page'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_compose_page'),
            array('uri' => $admin->generateUrl('compose', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_list_blocks'),
            array('uri' => $admin->generateUrl('sonata.page.admin.page|sonata.page.admin.block.list', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_list_snapshots'),
            array('uri' => $admin->generateUrl('sonata.page.admin.page|sonata.page.admin.snapshot.list', array('id' => $id)))
        );

        if (!$this->getSubject()->isHybrid() && !$this->getSubject()->isInternal()) {
            try {
                $menu->addChild(
                    $this->trans('view_page'),
                    array('uri' => $this->getRouteGenerator()->generate('page_slug', array('path' => $this->getSubject()->getUrl())))
                );
            } catch (\Exception $e) {
                // avoid crashing the admin if the route is not setup correctly
//                throw $e;
            }
        }
    }
}
