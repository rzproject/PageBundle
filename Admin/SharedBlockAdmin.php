<?php

namespace Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\SharedBlockAdmin as Admin;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\PageBundle\Entity\BaseBlock;

/**
 * Admin class for shared Block model.
 *
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class SharedBlockAdmin extends Admin
{
    /**
     * @var array
     */
    protected $blocks;

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('type')
            ->add('enabled', null, array('editable' => true))
            ->add('updatedAt')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var BaseBlock $block */
        $block = $this->getSubject();

        // New block
        if ($block->getId() === null) {
            $block->setType($this->request->get('type'));
        }

        $formMapper
            ->with('form.field_group_general')
                ->add('name', null, array('required' => true))
                ->add('enabled')
            ->end();

        $formMapper->with('form.field_group_options');

        /** @var BaseBlockService $service */
        $service = $this->blockManager->get($block);

        if ($block->getId() > 0) {
            $service->buildEditForm($formMapper, $block);
        } else {
            $service->buildCreateForm($formMapper, $block);
        }

        if ($block) {
            $blockType = $block->getType();
        }

        if ($blockType && $formMapper->has('settings') && isset($this->blocks[$blockType]['templates'])) {
            $settingsField = $formMapper->get('settings');

            if (!$settingsField->has('template')) {
                $choices = array();

                if (null !== $defaultTemplate = $this->getDefaultTemplate($service)) {
                    $choices[$defaultTemplate] = 'default';
                }

                foreach ($this->blocks[$blockType]['templates'] as $item) {
                    $choices[$item['template']] = $item['name'];
                }

                if (count($choices) > 1) {

                    $settingsField->add('template', 'choice', array('choices' => $choices));
                }
            }
        }

        $formMapper->end();
    }

    /**
     * {@inheritDoc).
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        // Filter on blocks without page and parents
        $query->andWhere($query->expr()->isNull($query->getRootAlias().'.page'));
        $query->andWhere($query->expr()->isNull($query->getRootAlias().'.parent'));

        return $query;
    }

    /**
     * @param BlockServiceInterface $blockService
     *
     * @return string|null
     */
    private function getDefaultTemplate(BlockServiceInterface $blockService)
    {
        $resolver = new OptionsResolver();
        $blockService->setDefaultSettings($resolver);
        $options = $resolver->resolve();

        if (isset($options['template'])) {
            return $options['template'];
        }

        return;
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param array $blocks
     */
    public function setBlocks($blocks)
    {
        $this->blocks = $blocks;
    }
}
