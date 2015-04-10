<?php


namespace Rz\PageBundle\Page\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sonata\SeoBundle\Seo\SeoPageInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Page\TemplateManagerInterface;

use Sonata\PageBundle\Page\Service\DefaultPageService as BasePageService;

/**
 * Default page service to render a page template.
 *
 * Note: this service is backward-compatible and functions like the old page renderer class.
 *
 * @author Olivier Paradis <paradis.olivier@gmail.com>
 */
class DefaultPageService extends BasePageService
{
    protected $router;

    /**
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param mixed $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * Updates the SEO page values for given page instance
     *
     * @param PageInterface $page
     */
    protected function updateSeoPage(PageInterface $page)
    {
        if (!$this->seoPage) {
            return;
        }

        if ($page->getTitle()) {
            $this->seoPage->setTitle($page->getTitle() ?: $page->getName());
        }

        if ($page->getMetaDescription()) {
            $this->seoPage->addMeta('name', 'description', $page->getMetaDescription());
        }

        if ($page->getMetaKeyword()) {
            $this->seoPage->addMeta('name', 'keywords', $page->getMetaKeyword());
        }

        if($page->getOgTitle()) {
            $this->seoPage->addMeta('property', 'og:title', $page->getOgTitle());
        }

        $this->seoPage->addMeta('property', 'og:type', $page->getOgType() ? $page->getOgType(): 'article');

        if($page->isCms()) {
            $this->seoPage->addMeta('property', 'og:url',  $this->router->generate($page, array(), true));
        }


        if($page->getOgDescription()) {
            $this->seoPage->addMeta('property', 'og:description', $page->getOgDescription());
        }

        $this->seoPage->addHtmlAttributes('prefix', 'og: http://ogp.me/ns#');
    }
}
