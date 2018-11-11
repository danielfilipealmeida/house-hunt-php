<?php

namespace App\Controller;

use App\Entity\Property;
use App\Service\Breadcrumb;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(
        Breadcrumb $breadcrumb
    ) {
        $breadcrumb->setPageTitle('All Properties');
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'pageTitle' => 'All Searches',
            'breadcrumb' => $breadcrumb->get()
        ]);   
    }

}