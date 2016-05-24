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
                $snapshotManager->generateRedirect($page, $snapshot);
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
}
