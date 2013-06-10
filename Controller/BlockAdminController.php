<?php

namespace Rz\PageBundle\Controller;

use Rz\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        return $this->render('SonataPageBundle:BlockAdmin:create.html.twig', array(
            'action' => 'create'
        ));
    }

}
