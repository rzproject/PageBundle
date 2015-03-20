<?php

namespace Rz\PageBundle\Entity;

use Sonata\PageBundle\Entity\BasePage;


abstract class Page extends BasePage
{
    protected $ogTitle;
    protected $ogType;
    protected $ogDescription;
    protected $ogImage;

    /**
     * @return mixed
     */
    public function getOgTitle()
    {
        return $this->ogTitle;
    }

    /**
     * @param mixed $ogTitle
     */
    public function setOgTitle($ogTitle)
    {
        $this->ogTitle = $ogTitle;
    }

    /**
     * @return mixed
     */
    public function getOgType()
    {
        return $this->ogType;
    }

    /**
     * @param mixed $ogType
     */
    public function setOgType($ogType)
    {
        $this->ogType = $ogType;
    }

    /**
     * @return mixed
     */
    public function getOgDescription()
    {
        return $this->ogDescription;
    }

    /**
     * @param mixed $ogDescription
     */
    public function setOgDescription($ogDescription)
    {
        $this->ogDescription = $ogDescription;
    }

    /**
     * @return mixed
     */
    public function getOgImage()
    {
        return $this->ogImage;
    }

    /**
     * @param mixed $ogImage
     */
    public function setOgImage($ogImage)
    {
        $this->ogImage = $ogImage;
    }



}
