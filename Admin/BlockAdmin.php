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

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $block = $this->getSubject();

        $page = false;

        if ($this->getParent()) {
            $page = $this->getParent()->getSubject();

            if (!$page instanceof PageInterface) {
                throw new \RuntimeException('The BlockAdmin must be attached to a parent PageAdmin');
            }

            if ($block->getId() === null) { // new block
                $block->setType($this->request->get('type'));
                $block->setPage($page);
            }

            if ($block->getPage()->getId() != $page->getId()) {
                throw new \RuntimeException('The page reference on BlockAdmin and parent admin are not the same');
            }
        }

        $isComposer = $this->hasRequest() ? $this->getRequest()->get('composer', false) : false;
        $generalGroupOptions = $optionsGroupOptions = array();
        if ($isComposer) {
            $generalGroupOptions['class'] = 'hidden';
            $optionsGroupOptions['name']  = '';
        }

        $formMapper->with('form.field_group_general', $generalGroupOptions);

        if (!$isComposer) {
            $formMapper->add('name');
        } else {
            $formMapper->add('name', 'hidden');
        }

        $formMapper->end();

        $isContainerRoot = $block && in_array($block->getType(), array('sonata.page.block.container', 'sonata.block.service.container')) && !$this->hasParentFieldDescription();
        $isStandardBlock = $block && !in_array($block->getType(), array('sonata.page.block.container', 'sonata.block.service.container')) && !$this->hasParentFieldDescription();

        if ($isContainerRoot || $isStandardBlock) {
            $formMapper->with('form.field_group_general', $generalGroupOptions);

            $service = $this->blockManager->get($block);

            $containerBlockTypes = $this->containerBlockTypes;

            // need to investigate on this case where $page == null ... this should not be possible
            if ($isStandardBlock && $page && !empty($containerBlockTypes)) {
                $formMapper->add('parent', 'entity', array(
                        'class'         => $this->getClass(),
                        'query_builder' => function (EntityRepository $repository) use ($page, $containerBlockTypes) {
                            return $repository->createQueryBuilder('a')
                                ->andWhere('a.page = :page AND a.type IN (:types)')
                                ->setParameters(array(
                                        'page'  => $page,
                                        'types' => $containerBlockTypes,
                                    ));
                        },
                    ), array(
                        'admin_code' => $this->getCode(),
                    ));
            }

            if ($isComposer) {
                $formMapper->add('enabled', 'hidden', array('data' => true));
            } else {
                $formMapper->add('enabled');
            }

            if ($isStandardBlock) {
                $formMapper->add('position', 'integer');
            }

            $formMapper->end();

            $formMapper->with('form.field_group_options', $optionsGroupOptions);

            if ($block->getId() > 0) {
                $service->buildEditForm($formMapper, $block);
            } else {
                $service->buildCreateForm($formMapper, $block);
            }

            $formMapper->end();
        } else {
            $formMapper
                ->with('form.field_group_options', $optionsGroupOptions)
                ->add('type', 'sonata_block_service_choice', array(
                        'context' => 'sonata_page_bundle',
                    ))
                ->add('enabled')
                ->add('position', 'integer')
                ->end()
            ;
        }
    }
}
