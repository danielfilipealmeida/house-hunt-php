<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Breadcrumb;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(Breadcrumb $breadcrumb)
    {
        $breadcrumb->setPageTitle('Homepage');
        
        return $this->render('static/homepage.html.twig', [
            'controller_name' => 'AppController',
            'pageTitle' => 'Homepage',
            'breadcrumb' => $breadcrumb->get()
        ]);
    }
}