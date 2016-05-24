<?php

namespace Rz\PageBundle\Entity;

use Sonata\PageBundle\Entity\PageManager as BasePageManager;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;

class PageManager extends BasePageManager
{
    /**
     * {@inheritdoc}
     */
    public function loadPages(SiteInterface $site)
    {
        $pages = $this->getEntityManager()
            ->createQuery(sprintf('SELECT p FROM %s p INDEX BY p.id WHERE p.site = %d ORDER BY p.position ASC', $this->class, $site->getId()))
            ->useResultCache(true, 3600)
            ->execute();

        foreach ($pages as $page) {
            $parent = $page->getParent();

            $page->disableChildrenLazyLoading();
            if (!$parent) {
                continue;
            }

            $pages[$parent->getId()]->disableChildrenLazyLoading();
            $pages[$parent->getId()]->addChildren($page);
        }

        return $pages;
    }

    /**
     * {@inheritdoc}
     */
    public function getHybridPages(SiteInterface $site)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from($this->class, 'p')
            ->where('p.routeName <> :routeName and p.site = :site')
            ->setParameters(array(
                'routeName' => PageInterface::PAGE_ROUTE_CMS_NAME,
                'site'      => $site->getId(),
            ))
            ->getQuery()
            ->useResultCache(true, 3600)
            ->execute();
    }


    /**
     * {@inheritdoc}
     */
    public function cleanupPages($pageIds)
    {
        $qb = $this->getRepository()->createQueryBuilder('page');
        $qb->delete()
            ->where($qb->expr()->in('page.id', $pageIds));

        return $qb->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function updatePagesTitles($pageIds, $title, $slug)
    {
        $qb = $this->getRepository()->createQueryBuilder('page');
        $qb->update()
            ->set('page.title', ':title')
            ->set('page.slug',  ':slug')
            ->set('page.edited',  ':edited')
            ->setParameter('title', $title)
            ->setParameter('slug', $slug)
            ->setParameter('edited', true)
            ->where($qb->expr()->in('page.id', $pageIds));
        return $qb->getQuery()->execute();
    }
}
