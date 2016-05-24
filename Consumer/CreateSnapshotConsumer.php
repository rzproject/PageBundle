<?php

namespace Rz\PageBundle\Consumer;

use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Sonata\PageBundle\Model\PageManagerInterface;
use Sonata\PageBundle\Model\SnapshotManagerInterface;
use Sonata\PageBundle\Model\TransformerInterface;
use Sonata\PageBundle\Consumer\CreateSnapshotConsumer as BaseCreateSnapshotConsumer;

class CreateSnapshotConsumer extends BaseCreateSnapshotConsumer
{

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $pageId = $event->getMessage()->getValue('pageId');

        $page = $this->pageManager->findOneBy(array('id' => $pageId));

        if (!$page) {
            return;
        }

        // start a transaction
        $this->snapshotManager->getConnection()->beginTransaction();

        // creating snapshot
        $snapshot = $this->transformer->create($page);

        // update the page status
        $page->setEdited(false);
        $this->pageManager->save($page);

        // save the snapshot
        $this->snapshotManager->save($snapshot);
        $this->snapshotManager->enableSnapshots(array($snapshot));

        //override for redirect
        $this->snapshotManager->generateRedirect($page, $snapshot);

        // commit the changes
        $this->snapshotManager->getConnection()->commit();
    }
}
