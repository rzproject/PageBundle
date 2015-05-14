<?php



namespace Rz\PageBundle\Entity;


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
