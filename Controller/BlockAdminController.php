<?php

namespace Rz\PageBundle\Controller;

use Rz\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sonata\BlockBundle\Exception\BlockNotFoundException;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Block Admin Controller
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BlockAdminController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function savePositionAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT')) {
            throw new AccessDeniedException();
        }

        try {
            $params = $this->get('request')->get('disposition');

            if (!is_array($params)) {
                throw new HttpException(400, 'wrong parameters');
            }

            $result = $this->get('sonata.page.block_interactor')->saveBlocksPosition($params);
            $status = 200;
        } catch (HttpException $e) {
            $status = $e->getStatusCode();
            $result = array(
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'code'      => $e->getCode()
            );

        } catch (\Exception $e) {
            $status = 500;
            $result = array(
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'code'      => $e->getCode()
            );
        }

        $result = ($result === true) ? 'ok' : $result;

        return $this->renderJson(array('result' => $result), $status);
    }

    public function saveTextBlockAction()
    {

        if (!$this->get('security.context')->isGranted('ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT')) {
            throw new AccessDeniedException();
        }

        $serializer = $this->get('serializer');
        $obj = array();

        if (!$this->get('security.context')->isGranted('ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT')) {
            $obj = array('status'=>'error', 'message'=>'You have no access for this resource');
            $serializer->serialize($obj, 'json');
            return $obj;
        }

        try {

            /** TODO: use JMSSerializeBundle
             *
             *  Add security feature
             * $blocks = $serializer->deserialize($this->get('request')->get('data'), 'Doctrine\Common\Collections\ArrayCollection' , 'json');
             *
             */
            $blocks = json_decode($this->get('request')->get('data'));
            $blockManager = $this->get('sonata.page.manager.block');

            foreach($blocks as $block) {
                $bloc = $blockManager->findOneBy(array('id'=>$block->id));
                if ($bloc) {
                    $bloc->setSetting('content',$block->content);
                }
                $blockManager->save($bloc);
            }

            $obj= array('status'=>'success', 'message'=>'Sucessfully saved edited blocks!');
            $serializer->serialize($obj, 'json');
            return $obj;
        } catch (\Exception $e) {
            $obj= array('status'=>'error', 'message'=>$e->getMessage());
            $serializer->serialize($obj, 'json');
            return $obj;
        }
    }


    /**
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws PageNotFoundException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        if (!$this->admin->getParent()) {
            throw new PageNotFoundException('You cannot create a block without a page');
        }

        $parameters = $this->admin->getPersistentParameters();

        if (!$parameters['type']) {
            return $this->render('SonataPageBundle:BlockAdmin:select_type.html.twig', array(
                'services'     => $this->get('sonata.block.manager')->getServices(),
                'base_template' => $this->getBaseTemplate(),
                'admin'         => $this->admin,
                'action'        => 'create'
            ));
        }

        return parent::createAction();
    }

    public function cmsBlockRenderAction($pageId = null,$blockId = null)
    {
        if (!$this->get('security.context')->isGranted('ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT')) {
            throw new AccessDeniedException();
        }

        $cmsManagerSelector = $this->get('sonata.page.cms_manager_selector');
        $cmsManager = $cmsManagerSelector->retrieve();

        $page  = $cmsManager->getPageById($pageId);
        $block = $cmsManager->getBlock($blockId);

        if (!$block instanceof BlockInterface) {
            throw new BlockNotFoundException(sprintf('Unable to find block identifier "%s".', $blockId));
        }
        $context = $this->get('sonata.block.context_manager')->get($block);

        $response = $this->get('sonata.block.renderer')->render($context);

        return $response;
    }

     /**
     * return the Response object associated to the edit action
     *
     *
     * @param mixed $id
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    public function editAction($id = null)
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $this->get('request')->get($this->admin->getIdParameter());

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

            $form->handleRequest($this->get('request'));
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->update($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                } else {
                    $this->addFlash('sonata_flash_success', 'flash_edit_success');
                }

                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash('sonata_flash_error', 'flash_edit_error');
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

    public function switchParentAction()
    {
        if (!$this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        $blockId  = $this->get('request')->get('block_id');
        $parentId = $this->get('request')->get('parent_id');
        if ($blockId === null or $parentId === null) {
            throw new HttpException(400, 'wrong parameters');
        }

        $block = $this->admin->getObject($blockId);
        if (!$block) {
            throw new PageNotFoundException(sprintf('Unable to find block with id %d', $blockId));
        }

        $parent = $this->admin->getObject($parentId);
        if (!$block) {
            throw new PageNotFoundException(sprintf('Unable to find parent block with id %d', $parentId));
        }

        $parent->addChildren($block);
        $this->admin->update($parent);

        return $this->renderJson(array('result' => 'ok'));
    }

}
