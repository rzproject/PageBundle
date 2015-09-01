<?php

namespace Rz\PageBundle\Controller;

use Sonata\PageBundle\Controller\PageAdminController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Admin\BaseFieldDescription;


class PageAdminController extends Controller
{
    /**
     * @param mixed $query
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function batchActionSnapshot($query)
    {
        if (!$this->get('security.context')->isGranted('ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT')) {
            throw new AccessDeniedException();
        }

        foreach ($query->execute() as $page) {
            $this->get('sonata.notification.backend')
                ->createAndPublish('sonata.page.create_snapshot', array(
                    'pageId' => $page->getId(),
                ));
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        if ($request->getMethod() == 'GET' && !$request->get('siteId')) {
            $sites = $this->get('sonata.page.manager.site')->findBy(array());

            if (count($sites) == 1) {

                return $this->redirect($this->admin->generateUrl('create', array(
                    'siteId' => $sites[0]->getId(),
                    'uniqid' => $this->admin->getUniqid()
                )));
            }

            try {
                $current = $this->get('sonata.page.site.selector')->retrieve();
            } catch (\RuntimeException $e) {
                $current = false;
            }

            return $this->render('SonataPageBundle:PageAdmin:select_site.html.twig', array(
                'sites'   => $sites,
                'current' => $current,
            ));
        }

        return parent::createAction($request);
    }

    /**
     * return the Response object associated to the edit action
     *
     *
     * @param mixed $id
     *
     * @param Request $request
     * @return Response
     */
    public function editAction($id = null, Request $request = null)
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->getRestMethod() == 'POST') {

            $form->handleRequest($request);

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->update($object);

                $this->addFlash(
                    'sonata_flash_success',
                    $this->admin->trans(
                        'flash_edit_success',
                        array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                        'SonataAdminBundle'
                    )
                );

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    if (!$this->isXmlHttpRequest()) {
                        $this->addFlash(
                            'sonata_flash_error',
                            $this->admin->trans(
                                'flash_edit_error',
                                array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                                'SonataAdminBundle'
                            )
                        );
                    }
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form'   => $view,
            'object' => $object,
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function composeAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('EDIT') || false === $this->get('sonata.page.admin.block')->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $id   = $request->get($this->admin->getIdParameter());
        $page = $this->admin->getObject($id);
        if (!$page) {
            throw new NotFoundHttpException(sprintf('unable to find the page with id : %s', $id));
        }

        $containers       = array();
        $orphanContainers = array();
        $children         = array();

        $templateManager    = $this->get('sonata.page.template_manager');
        $template           = $templateManager->get($page->getTemplateCode());
        $templateContainers = $template->getContainers();

        foreach ($templateContainers as $id => $container) {
            $containers[$id] = array(
                'area' => $container,
                'block' => false,
            );
        }

        // 'attach' containers to corresponding template area, otherwise add it to orphans
        foreach ($page->getBlocks() as $block) {
            $blockCode = $block->getSetting('code');
            if ($block->getParent() === null) {
                if (isset($containers[$blockCode])) {
                    $containers[$blockCode]['block'] = $block;
                } else {
                    $orphanContainers[] = $block;
                }
            } else {
                $children[] = $block;
            }
        }

        // searching for block defined in template which are not created
        $blockInteractor = $this->get('sonata.page.block_interactor');

        foreach ($containers as $id => $container) {

            if ($container['block'] === false && $templateContainers[$id]['shared'] === false) {
                $blockContainer = $blockInteractor->createNewContainer(array(
                    'page' => $page,
                    'name' => $templateContainers[$id]['name'],
                    'code' => $id,
                ));

                $containers[$id]['block'] = $blockContainer;
            }
        }

        $csrfProvider = $this->get('form.csrf_provider');

        return $this->render('RzPageBundle:PageAdmin:compose.html.twig', array(
            'object'           => $page,
            'action'           => 'edit',
            'template'         => $template,
            'page'             => $page,
            'containers'       => $containers,
            'orphanContainers' => $orphanContainers,
            'csrfTokens'       => array(
                'remove' => $csrfProvider->generateCsrfToken('sonata.delete'),
            ),
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function composeContainerShowAction(Request $request = null)
    {
        if (false === $this->get('sonata.page.admin.block')->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $id    = $request->get($this->admin->getIdParameter());
        $block = $this->get('sonata.page.admin.block')->getObject($id);
        if (!$block) {
            throw new NotFoundHttpException(sprintf('unable to find the block with id : %s', $id));
        }

        $blockServices = $this->get('sonata.block.manager')->getServicesByContext('sonata_page_bundle', false);
        $userDefinedBlocks = $this->getUserDefinedBlocks($block);

        return $this->render('RzPageBundle:PageAdmin:compose_container_show.html.twig', array(
            'blockServices'    => $userDefinedBlocks ?: $blockServices,
            'container'        => $block,
            'page'             => $block->getPage()
        ));
    }

    protected function getUserDefinedBlocks($block) {

        $templateManager    = $this->get('sonata.page.template_manager');

        if(!$templateCode = $block->getPage()->getTemplateCode()) {
            return null;
        }

        if (!$template = $templateManager->get($templateCode)) {
            return null;
        }

        $containers = $template->getContainers();
        $settings = $block->getSettings();
        if (!isset($settings['code'])) {
            return null;
        }

        if (!isset($containers[$settings['code']])) {
            return null;
        }

        $templateSettings = $containers[$settings['code']];

        if (!isset($templateSettings['blocks'])) {
            return null;
        }

        if(count($templateSettings['blocks']) > 0) {
            $blockManager = $this->get('sonata.block.manager');
            $blockservises = array();
            foreach ($templateSettings['blocks'] as $block) {
                $blockservises[$block] = $blockManager->getService($block);
            }
            return $blockservises;
        } else {
            return null;
        }

        return null;

    }

}
