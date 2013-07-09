<?php

namespace Rz\PageBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Sonata\PageBundle\Exception\InternalErrorException;

use Sonata\PageBundle\Listener\ResponseListener as BaseResponseListener;

/**
 * This class redirect the onCoreResponse event to the correct
 * cms manager upon user permission
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ResponseListener extends BaseResponseListener
{

    /**
     * Filter the `core.response` event to decorate the action
     *
     * @param FilterResponseEvent $event
     *
     * @return void
     *
     * @throws InternalErrorException
     */
    public function onCoreResponse(FilterResponseEvent $event)
    {
        $cms = $this->cmsSelector->retrieve();

        $response = $event->getResponse();
        $request  = $event->getRequest();

        if ($this->cmsSelector->isEditor()) {
            $response->setPrivate();

            if (!$request->cookies->has('sonata_page_is_editor')) {
                $response->headers->setCookie(new Cookie('sonata_page_is_editor', 1));
            }
        }

        $page = $cms->getCurrentPage();

        // display a validation page before redirecting, so the editor can edit the current page
        if ($page && $response->isRedirection() && $this->cmsSelector->isEditor() && !$request->get('_sonata_page_skip')) {
            $response = new Response($this->templating->render('SonataPageBundle:Page:redirect.html.twig', array(
                                                                                                             'response'   => $response,
                                                                                                             'page'       => $page,
                                                                                                         )));

            $response->setPrivate();

            $event->setResponse($response);

            return;
        }

        if (!$this->decoratorStrategy->isDecorable($event->getRequest(), $event->getRequestType(), $response)) {
            return;
        }

        if (!$this->cmsSelector->isEditor() && $request->cookies->has('sonata_page_is_editor')) {
            $response->headers->clearCookie('sonata_page_is_editor');
        }

        if (!$page) {
            throw new InternalErrorException('No page instance available for the url, run the sonata:page:update-core-routes and sonata:page:create-snapshots commands');
        }

        // only decorate hybrid page or page with decorate = true
        if (!$page->isHybrid() || !$page->getDecorate()) {
            return;
        }

        $parameters = array('content' => $response->getContent());
        $response = $this->pageServiceManager->execute($page, $request, $parameters, $response);

        if (!$this->cmsSelector->isEditor() && $page->isCms()) {
            $response->setTtl($page->getTtl());
        }

        $event->setResponse($response);
    }
}