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

use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\PageBundle\Admin\BlockAdmin as BaseBlockAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class BlockAdmin extends BaseBlockAdmin
{

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('savePosition', 'save-position');
        $collection->add('saveTextBlock', 'save-text-block',
                         array('_controller' => sprintf('%s:%s', $this->baseControllerName, 'saveTextBlock')));

        $collection->add('cmsBlockRender', 'cms-render-block/{pageId}/{blockId}',
                         array('_controller' => sprintf('%s:%s', $this->baseControllerName, 'cmsBlockRender')));

        $collection->add('view', $this->getRouterIdParameter().'/view');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('type', null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('name', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabled', null, array('footable'=>array('attr'=>array('data_hide'=>'phone'))))
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
            ->add('type')
            ->add('name')
            ->add('enabled')
            ->add('updatedAt')
            ->add('position')
        ;
    }
}
