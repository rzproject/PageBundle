<?php



namespace Rz\PageBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\PageBundle\Model\PageManagerInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\Page;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\PageBundle\Entity\PageManager as BasePageManager;


class PageManager extends BasePageManager
{

    /**
     * {@inheritdoc}
     */
    public function findDuplicateUrl($parent, $page, $url)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from( $this->class, 'p')
            ->where('p.parent = :parent and p.slug = :slug')
            ->setParameters(array(
                'parent' => $parent->getId(),
                'slug' => $url
            ))
            ->getQuery()
            ->execute();
    }
}
