<?php

namespace Rz\PageBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\PageBundle\Entity\SnapshotManager as BaseSnapshotManager;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SnapshotManagerInterface;
use Sonata\PageBundle\Model\SnapshotPageProxy;
use Symfony\Component\DependencyInjection\Reference;
use Sonata\PageBundle\Model\SnapshotInterface;

/**
 * This class manages SnapshotInterface persistency with the Doctrine ORM.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SnapshotManager extends BaseSnapshotManager
{

    protected $redirectManager;

    /**
     * @return mixed
     */
    public function getRedirectManager()
    {
        return $this->redirectManager;
    }

    /**
     * @param mixed $redirectManager
     */
    public function setRedirectManager($redirectManager)
    {
        $this->redirectManager = $redirectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findEnableSnapshot(array $criteria)
    {
        $date = new \Datetime();
        $parameters = array(
            'publicationDateStart' => $date,
            'publicationDateEnd'   => $date,
        );

        $query = $this->getRepository()
            ->createQueryBuilder('s')
            ->andWhere('s.publicationDateStart <= :publicationDateStart AND ( s.publicationDateEnd IS NULL OR s.publicationDateEnd >= :publicationDateEnd )');

        if (isset($criteria['site'])) {
            $query->andWhere('s.site = :site');
            $parameters['site'] = $criteria['site'];
        }

        if (isset($criteria['pageId'])) {
            $query->andWhere('s.page = :page');
            $parameters['page'] = $criteria['pageId'];
        } elseif (isset($criteria['url'])) {
            $query->andWhere('s.url = :url');
            $parameters['url'] = $criteria['url'];
        } elseif (isset($criteria['routeName'])) {
            $query->andWhere('s.routeName = :routeName');
            $parameters['routeName'] = $criteria['routeName'];
        } elseif (isset($criteria['pageAlias'])) {
            $query->andWhere('s.pageAlias = :pageAlias');
            $parameters['pageAlias'] = $criteria['pageAlias'];
        } elseif (isset($criteria['name'])) {
            $query->andWhere('s.name = :name');
            $parameters['name'] = $criteria['name'];
        } else {
            throw new \RuntimeException('please provide a `pageId`, `url`, `routeName` or `name` as criteria key');
        }

        $query->setMaxResults(1);
        $query->setParameters($parameters);

        return $query->getQuery()
                     ->useResultCache(true, 3600)
                     ->getOneOrNullResult();
    }

    /**
     * return a page with the given routeName.
     *
     * @param string $routeName
     *
     * @return PageInterface|false
     */
    public function getPageByName($routeName)
    {
        $snapshots = $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from($this->class, 's')
            ->where('s.routeName = :routeName')
            ->setParameters(array(
                'routeName' => $routeName,
            ))
            ->getQuery()
            ->useResultCache(true, 3600)
            ->execute();

        $snapshot = count($snapshots) > 0 ? $snapshots[0] : false;

        if ($snapshot) {
            return new SnapshotPageProxy($this, $snapshot);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function findPreviousSnapshot(array $criteria)
    {
        $date = new \Datetime();
        $parameters = array(
            'publicationDateStart' => $date,
            'publicationDateEnd'   => $date,
        );

        $query = $this->getRepository()
            ->createQueryBuilder('s')
            ->andWhere('s.publicationDateStart <= :publicationDateStart AND ( s.publicationDateEnd IS NULL OR s.publicationDateEnd >= :publicationDateEnd )');

        if (isset($criteria['site'])) {
            $query->andWhere('s.site = :site');
            $parameters['site'] = $criteria['site'];
        }

        if (isset($criteria['pageId'])) {
            $query->andWhere('s.page = :page');
            $parameters['page'] = $criteria['pageId'];
        } elseif (isset($criteria['url'])) {
            $query->andWhere('s.url = :url');
            $parameters['url'] = $criteria['url'];
        } elseif (isset($criteria['routeName'])) {
            $query->andWhere('s.routeName = :routeName');
            $parameters['routeName'] = $criteria['routeName'];
        } elseif (isset($criteria['pageAlias'])) {
            $query->andWhere('s.pageAlias = :pageAlias');
            $parameters['pageAlias'] = $criteria['pageAlias'];
        } elseif (isset($criteria['name'])) {
            $query->andWhere('s.name = :name');
            $parameters['name'] = $criteria['name'];
        } else {
            throw new \RuntimeException('please provide a `pageId`, `url`, `routeName` or `name` as criteria key');
        }

        $query->orderBy('s.id', 'DESC');

       $query->setMaxResults(1)->setFirstResult(1);
        $query->setParameters($parameters);

        return $query->getQuery()
            ->useResultCache(true, 3600)
            ->getOneOrNullResult();
    }

    public function generateRedirect(PageInterface $page, SnapshotInterface $snapshot, $redirectType = '301') {


        $redirectTypes = $this->redirectManager->getRedirectTypes();
        $redirectType = array_key_exists($redirectType, $redirectTypes) ? $redirectType : $this->redirectManager->getDefaultRedirect();
        $previous = $this->findPreviousSnapshot(['pageId'=>$snapshot->getPage(), 'site'=>$snapshot->getPage()->getSite()]);

        if($previous && ($snapshot->getUrl() !== $previous->getUrl())) {
            $redirect = $this->getRedirectManager()->create();
            $redirect->setName($page->getTitle());
            $redirect->setEnabled(true);
            $redirect->setType('page');
            $redirect->setReferenceId($snapshot->getPage()->getId());
            $redirect->setFromPath($previous->getUrl());
            $redirect->setToPath($snapshot->getUrl());
            $redirect->setRedirect($redirectType);
            $redirect->setPublicationDateStart($snapshot->getPublicationDateStart());
            $redirect->setPublicationDateEnd($snapshot->getPublicationDateEnd());
            $this->getRedirectManager()->save($redirect);

            //redirect old redirects
            $this->getRedirectManager()->fixOldRedirects(['referenceId'=>$redirect->getReferenceId(),
                                                          'type'=>$redirect->getType(),
                                                          'toPath'=>$redirect->getToPath(),
                                                          'currentId'=>$redirect->getId(),
                                                          'redirect'=>$redirect->getRedirect()]);
        }
    }
}
