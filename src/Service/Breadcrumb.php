<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;

class Breadcrumb {

     /**
      * @var array breadcrumbData
      */
    private $breadcrumb;

    /**
     * the page title
     *
     * @var $string
     */
    private $pageTitle;

    

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(RouterInterface $router)
    {
        $homepageRoute = $router->generate('homepage');        

        $this->breadcrumb = [ 'Homepage' => $homepageRoute];
    }

    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Gets the current breadcrumb
     *
     * @return array
     */
    public function get(): array
    {
        $finalBreadcrumb = $this->breadcrumb;
        $finalBreadcrumb[$this->pageTitle] = null;

        return $finalBreadcrumb;
    }

    /**
     * Adds a new level to the breadcrumb
     *
     * @param [type] $title
     * @param [type] $url
     * @return void
     */
    public function add($title, $url) {
        $this->breadcrumb[$title] = $url;
    }


}