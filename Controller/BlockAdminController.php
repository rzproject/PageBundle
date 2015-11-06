<?php

namespace Rz\PageBundle\Controller;

use Sonata\PageBundle\Controller\BlockAdminController as Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Block Admin Controller.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BlockAdminController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request = null)
    {
        $this->admin->checkAccess('create');

        $sharedBlockAdminClass = $this->container->getParameter('sonata.page.admin.shared_block.class');
        if (!$this->admin->getParent() && get_class($this->admin) !== $sharedBlockAdminClass) {
            throw new PageNotFoundException('You cannot create a block without a page');
        }

        $parameters = $this->admin->getPersistentParameters();

        if (!$parameters['type']) {
            return $this->render('RzPageBundle:BlockAdmin:select_type.html.twig', array(
                'services'      => $this->get('sonata.block.manager')->getServicesByContext('sonata_page_bundle'),
                'base_template' => $this->getBaseTemplate(),
                'admin'         => $this->admin,
                'action'        => 'create',
            ));
        }

        return parent::createAction($request);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Sonata\PageBundle\Exception\PageNotFoundException
     */
    public function composePreviewAction(Request $request = null)
    {
        $this->admin->checkAccess('composePreview');

        $blockId = $request->get('block_id');

        /** @var \Sonata\PageBundle\Entity\BaseBlock $block */
        $block = $this->admin->getObject($blockId);
        if (!$block) {
            throw new PageNotFoundException(sprintf('Unable to find block with id %d', $blockId));
        }

        $container = $block->getParent();
        if (!$container) {
            throw new PageNotFoundException('No parent found, unable to preview an orphan block');
        }

        $blockServices = $this->get('sonata.block.manager')->getServicesByContext('sonata_page_bundle', false);

        return $this->render('RzPageBundle:BlockAdmin:compose_preview.html.twig', array(
            'container'     => $container,
            'child'         => $block,
            'blockServices' => $blockServices,
        ));
    }
}
