<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Breadcrumb;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(Breadcrumb $breadcrumb)
    {
        dump('!!!!');
        $breadcrumb->setPageTitle('Homepage');

        return $this->render('static/homepage.html.twig', [
            'controller_name' => 'AppController',
            'pageTitle' => 'Homepage',
            'breadcrumb' => $breadcrumb->get(),
        ]);
    }
}
