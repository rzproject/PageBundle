<?php

/*
 * This file is part of the RzPageBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\PageBundle\Block;

use Sonata\PageBundle\Block\ChildrenPagesBlockService as BaseChildrenPagesBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;

use Sonata\PageBundle\Exception\PageNotFoundException;
use Sonata\PageBundle\Site\SiteSelectorInterface;
use Sonata\PageBundle\CmsManager\CmsManagerSelectorInterface;

/**
 * Render children pages
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ChildrenPagesBlockService extends BaseChildrenPagesBlockService
{
    /**
     * @param string                                                    $name
     * @param \Symfony\Component\Templating\EngineInterface             $templating
     * @param \Sonata\PageBundle\Site\SiteSelectorInterface             $siteSelector
     * @param \Sonata\PageBundle\CmsManager\CmsManagerSelectorInterface $cmsManagerSelector
     */
    public function __construct($name, EngineInterface $templating, SiteSelectorInterface $siteSelector, CmsManagerSelectorInterface $cmsManagerSelector)
    {
        parent::__construct($name, $templating, $siteSelector, $cmsManagerSelector);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $cmsManager = $this->cmsManagerSelector->retrieve();

        if ($settings['current']) {
            $page = $cmsManager->getCurrentPage();
        } elseif ($settings['pageId']) {
            $page = $settings['pageId'];
        } else {
            if ($this->siteSelector->retrieve()) {
                try {
                    $page = $cmsManager->getPage($this->siteSelector->retrieve(), '/');
                } catch (PageNotFoundException $e) {
                    $page = false;
                }
            } else {
                $block = $blockContext->getBlock();
                $page = $block->getPage();
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'page'     => $page,
            'block'    => $blockContext->getBlock(),
            'settings' => $settings
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('title', 'text', array(
                  'required' => false
                )),
                array('current', 'checkbox', array(
                  'required' => false
                )),
                array('pageId', 'sonata_page_selector', array(
                    'model_manager' => $formMapper->getAdmin()->getModelManager(),
                    'class'         => 'Application\Sonata\PageBundle\Entity\Page',
                    'site'          => $block->getPage()->getSite(),
                    'required'      => false
                )),
                array('class', 'text', array(
                  'required' => false
                )),
            )
        ));
    }
}
