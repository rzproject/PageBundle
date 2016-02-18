<?php

namespace Rz\PageBundle\Controller;

use Sonata\PageBundle\Controller\SnapshotAdminController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SnapshotInterface;

/**
 * Snapshot Admin Controller.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SnapshotAdminController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function createAction(Request $request = null)
    {
        $this->admin->checkAccess('create');

        $class = $this->get('sonata.page.manager.snapshot')->getClass();

        $pageManager = $this->get('sonata.page.manager.page');

        $snapshot = new $class();

        if ($request->getMethod() == 'GET' && $request->get('pageId')) {
            $page = $pageManager->findOne(array('id' => $request->get('pageId')));
        } elseif ($this->admin->isChild()) {
            $page = $this->admin->getParent()->getSubject();
        } else {
            $page = null; // no page selected ...
        }

        $snapshot->setPage($page);

        $form = $this->createForm('sonata_page_create_snapshot', $snapshot);

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $snapshotManager = $this->get('sonata.page.manager.snapshot');
                $transformer = $this->get('sonata.page.transformer');

                $page = $form->getData()->getPage();
                $page->setEdited(false);

                $snapshot = $transformer->create($page);

                $this->admin->create($snapshot);

                $pageManager->save($page);

                $snapshotManager->enableSnapshots(array($snapshot));

                //override for redirect
                $this->generateRedirect($page, $snapshot);
            }

            return $this->redirect($this->admin->generateUrl('edit', array(
                'id' => $snapshot->getId(),
            )));
        }

        return $this->render('SonataPageBundle:SnapshotAdmin:create.html.twig', array(
            'action'  => 'create',
            'form'    => $form->createView(),
        ));
    }

    protected function generateRedirect(PageInterface $page, SnapshotInterface $snapshot) {
        $snapshotManager = $this->get('sonata.page.manager.snapshot');
        $previous = $snapshotManager->findPreviousSnapshot(['pageId'=>$snapshot->getPage(), 'site'=>$snapshot->getPage()->getSite()]);
        if($previous && ($snapshot->getUrl() !== $previous->getUrl())) {
            $redirectManager = $this->get('rz.redirect.manager.redirect');
            $redirect = $redirectManager->create();
            $redirect->setName($page->getTitle());
            $redirect->setEnabled(true);
            $redirect->setType('page');
            $redirect->setReferenceId($snapshot->getPage()->getId());
            $redirect->setFromPath($previous->getUrl());
            $redirect->setToPath($snapshot->getUrl());
            $redirect->setPublicationDateStart($snapshot->getPublicationDateStart());
            $redirect->setPublicationDateEnd($snapshot->getPublicationDateEnd());
            $redirectManager->save($redirect);

            //redirect old redirects
            $redirectManager->fixOldRedirects(array('referenceId'=>$redirect->getReferenceId(),
                                                    'type'=>$redirect->getType(),
                                                    'toPath'=>$redirect->getToPath(),
                                                    'currentId'=>$redirect->getId()));
        }
    }
}
