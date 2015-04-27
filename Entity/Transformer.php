<?php


namespace Rz\PageBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Model\BlockManagerInterface;
use Sonata\PageBundle\Model\PageManagerInterface;
use Sonata\PageBundle\Model\SnapshotManagerInterface;
use Sonata\PageBundle\Model\TransformerInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SnapshotInterface;
use Sonata\PageBundle\Model\SnapshotPageProxy;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Sonata\PageBundle\Entity\Transformer as BaseTransformer;


class Transformer extends BaseTransformer
{

    /**
     * {@inheritdoc}
     */
    public function create(PageInterface $page)
    {
        $snapshot = $this->snapshotManager->create();

        $snapshot->setPage($page);
        $snapshot->setUrl($page->getUrl());
        $snapshot->setEnabled($page->getEnabled());
        $snapshot->setRouteName($page->getRouteName());
        $snapshot->setPageAlias($page->getPageAlias());
        $snapshot->setType($page->getType());
        $snapshot->setName($page->getName());
        $snapshot->setPosition($page->getPosition());
        $snapshot->setDecorate($page->getDecorate());

        if (!$page->getSite()) {
            throw new \RuntimeException(sprintf('No site linked to the page.id=%s', $page->getId()));
        }

        $snapshot->setSite($page->getSite());

        if ($page->getParent()) {
            $snapshot->setParentId($page->getParent()->getId());
        }

        if ($page->getTarget()) {
            $snapshot->setTargetId($page->getTarget()->getId());
        }

        $content                     = array();
        $content['id']               = $page->getId();
        $content['name']             = $page->getName();
        $content['javascript']       = $page->getJavascript();
        $content['stylesheet']       = $page->getStylesheet();
        $content['raw_headers']      = $page->getRawHeaders();
        $content['title']            = $page->getTitle();
        $content['meta_description'] = $page->getMetaDescription();
        $content['meta_keyword']     = $page->getMetaKeyword();

        $content['og_title']         = $page->getOgTitle();
        $content['og_type']          = $page->getOgType();
        $content['og_description']   = $page->getOgDescription();
        $content['og_image']         = $page->getOgImage();

        $content['template_code']    = $page->getTemplateCode();
        $content['request_method']   = $page->getRequestMethod();
        $content['created_at']       = $page->getCreatedAt()->format('U');
        $content['updated_at']       = $page->getUpdatedAt()->format('U');
        $content['slug']             = $page->getSlug();
        $content['parent_id']        = $page->getParent() ? $page->getParent()->getId() : null;
        $content['target_id']        = $page->getTarget() ? $page->getTarget()->getId() : null;

        $content['blocks'] = array();
        foreach ($page->getBlocks() as $block) {
            if ($block->getParent()) { // ignore block with a parent => must be a child of a main
                continue;
            }

            $content['blocks'][] = $this->createBlocks($block);
        }

        $snapshot->setContent($content);

        return $snapshot;
    }

    /**
     * {@inheritdoc}
     */
    public function load(SnapshotInterface $snapshot)
    {
        $page = $this->pageManager->create();

        $page->setRouteName($snapshot->getRouteName());
        $page->setPageAlias($snapshot->getPageAlias());
        $page->setType($snapshot->getType());
        $page->setCustomUrl($snapshot->getUrl());
        $page->setUrl($snapshot->getUrl());
        $page->setPosition($snapshot->getPosition());
        $page->setDecorate($snapshot->getDecorate());
        $page->setSite($snapshot->getSite());
        $page->setEnabled($snapshot->getEnabled());

        $content = $this->fixPageContent($snapshot->getContent());

        $page->setId($content['id']);
        $page->setJavascript($content['javascript']);
        $page->setStylesheet($content['stylesheet']);
        $page->setRawHeaders($content['raw_headers']);
        $page->setTitle($content['title']);
        $page->setMetaDescription($content['meta_description']);
        $page->setMetaKeyword($content['meta_keyword']);

        if(isset($content['og_title'])) {
            $page->setOgTitle($content['og_title']);
        }

        if(isset($content['og_type'])) {
            $page->setOgType($content['og_type']);
        }

        if(isset($content['og_description'])) {
            $page->setOgDescription($content['og_description']);
        }

        if(isset($content['og_image'])) {
            $page->setOgImage($content['og_image']);
        }


        $page->setName($content['name']);
        $page->setSlug($content['slug']);
        $page->setTemplateCode($content['template_code']);
        $page->setRequestMethod($content['request_method']);

        $createdAt = new \DateTime;
        $createdAt->setTimestamp($content['created_at']);
        $page->setCreatedAt($createdAt);

        $updatedAt = new \DateTime;
        $updatedAt->setTimestamp($content['updated_at']);
        $page->setUpdatedAt($updatedAt);

        return $page;
    }
}