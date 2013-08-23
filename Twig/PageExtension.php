<?php

namespace Rz\PageBundle\Twig;

use Sonata\PageBundle\Model\PageBlockInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\CmsManager\CmsManagerSelectorInterface;
use Sonata\PageBundle\Site\SiteSelectorInterface;
use Sonata\PageBundle\Model\SnapshotPageProxy;
use Symfony\Component\Routing\RouterInterface;

use Sonata\BlockBundle\Templating\Helper\BlockHelper;

class PageExtension extends \Twig_Extension
{
    /**
     * @var CmsManagerSelectorInterface
     */
    private $cmsManagerSelector;

    /**
     * @var SiteSelectorInterface
     */
    private $siteSelector;

    /**
     * @var array
     */
    private $resources;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var BlockHelper
     */
    private $blockHelper;

    /**
     * Constructor
     *
     * @param CmsManagerSelectorInterface $cmsManagerSelector A CMS manager selector
     * @param SiteSelectorInterface       $siteSelector       A site selector
     * @param RouterInterface             $router             The Router
     * @param BlockHelper                 $blockHelper        The Block Helper
     */
    public function __construct(CmsManagerSelectorInterface $cmsManagerSelector,
                                SiteSelectorInterface $siteSelector,
                                RouterInterface $router,
                                BlockHelper $blockHelper)
    {
        $this->cmsManagerSelector = $cmsManagerSelector;
        $this->siteSelector       = $siteSelector;
        $this->router             = $router;
        $this->blockHelper        = $blockHelper;
    }

    public function getFunctions()
    {
        return array(
            'rz_render_page_url' => new \Twig_SimpleFunction('rz_render_page_url', array($this, 'renderUrlByName')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function renderUrlByName($value)
    {

        $cmsManager = $this->cmsManagerSelector->retrieve();
        $page = $cmsManager->getPageByName($this->siteSelector->retrieve(), $value);

       return $page;
    }

    public function getName()
    {
        return 'rz_page_extension';
    }
}
