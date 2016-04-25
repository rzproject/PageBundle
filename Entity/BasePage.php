<?php
namespace Rz\PageBundle\Entity;

use Sonata\PageBundle\Entity\BasePage as Page;

abstract class BasePage extends Page
{
    protected $canonicalPage;

    /**
     * @return mixed
     */
    public function getCanonicalPage()
    {
        return $this->canonicalPage;
    }

    /**
     * @param mixed $canonicalPage
     */
    public function setCanonicalPage($canonicalPage)
    {
        $this->canonicalPage = $canonicalPage;
    }
}
