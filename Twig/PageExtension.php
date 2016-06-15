<?php

namespace Rz\PageBundle\Twig;

use Sonata\PageBundle\CmsManager\CmsManagerSelectorInterface;
use Sonata\PageBundle\Site\SiteSelectorInterface;
use Symfony\Component\Routing\RouterInterface;
use Sonata\PageBundle\Exception\PageNotFoundException;
use Sonata\BlockBundle\Templating\Helper\BlockHelper;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Sonata\CoreBundle\Model\ManagerInterface;

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

    private $seoPage;

    private $requestStack;

    private $pageManager;

    /**
     * Constructor
     *
     * @param RequestStack $requestStack
     * @param RouterInterface $router                         The Router
     * @param CmsManagerSelectorInterface $cmsManagerSelector A CMS manager selector
     * @param SiteSelectorInterface $siteSelector             A site selector
     * @param BlockHelper $blockHelper                        The Block Helper
     * @param SeoPageInterface $seoPage
     */
    public function __construct(RequestStack $requestStack,
                                RouterInterface $router,
                                CmsManagerSelectorInterface $cmsManagerSelector,
                                SiteSelectorInterface $siteSelector,
                                BlockHelper $blockHelper,
                                SeoPageInterface $seoPage,
                                ManagerInterface $pageManager)
    {
        $this->requestStack       = $requestStack;
        $this->router             = $router;
        $this->cmsManagerSelector = $cmsManagerSelector;
        $this->siteSelector       = $siteSelector;
        $this->blockHelper        = $blockHelper;
        $this->seoPage            = $seoPage;
        $this->pageManager        = $pageManager;
    }

    public function getFunctions()
    {
        return array(
            'rz_page_render_page_url' => new \Twig_SimpleFunction('rz_page_render_page_url', array($this, 'renderUrlByName')),
            'rz_page_render_page_alias_url' => new \Twig_SimpleFunction('rz_page_render_page_alias_url', array($this, 'renderUrlByAlias')),
            'rz_page_get_page_url' => new \Twig_SimpleFunction('rz_page_get_page_url', array($this, 'getUrlByPage')),
            'rz_page_object' => new \Twig_SimpleFunction('rz_page_object', array($this, 'getPageObject')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getPageObject($value=null)
    {
        if(!$value) {
            return null;
        }

        $page = $this->pageManager->findOneBy(array('id'=>$value));

        if(!$page) {
            return null;
        }

        return $page;
    }


    public function getUrlByPage($value)
    {
        if(!$this->seoPage->getLinkCanonical()) {
            return;
        }

        try {
            $host = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
            $baseUrl = $this->requestStack->getCurrentRequest()->getBaseUrl();
            return str_replace(sprintf('%s%s', $host, $baseUrl), '', $this->seoPage->getLinkCanonical());
        } catch(PageNotFoundException $e) {
            return;
        }
    }

    public function renderUrlByName($value)
    {
        try {
            $cmsManager = $this->cmsManagerSelector->retrieve();
            $page = $cmsManager->getPageByName($this->siteSelector->retrieve(), $value);
            return $page;
        } catch(PageNotFoundException $e) {
            return;
        }
    }

    public function renderUrlByAlias($value)
    {
        try {
            $cmsManager = $this->cmsManagerSelector->retrieve();
            $page = $cmsManager->getPageByPageAlias($this->siteSelector->retrieve(), $value);
            return $page;
        } catch(PageNotFoundException $e) {
            return;
        }
    }

    public function getName()
    {
        return 'rz_page_extension';
    }
}
