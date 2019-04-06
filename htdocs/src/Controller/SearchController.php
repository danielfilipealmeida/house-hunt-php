<?php

namespace App\Controller;

use App\Entity\Search;
use App\Service\Breadcrumb;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(
        Breadcrumb $breadcrumb
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $searches = $this->getDoctrine()
            ->getRepository(Search::class)
            ->findAll();

        $breadcrumb->setPageTitle('All Searches');

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'pageTitle' => 'All Searches',
            'searches' => $searches,
            'breadcrumb' => $breadcrumb->get(),
        ]);
    }

    /**
     * @Route("/search/{id}", name="search_show", requirements={"id"="\d+"})
     */
    public function show(Search $search, Breadcrumb $breadcrumb)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$search) {
            throw $this->createNotFoundException('No search found for id '.$id);
        }

        $breadcrumb->setPageTitle($search->getTitle());
        $breadcrumb->add('All Searches', $this->generateUrl('search'));

        return $this->render(
            'search/show.html.twig',
            [
                'search' => $search,
                'breadcrumb' => $breadcrumb->get(),
            ]
        );
    }

    /**
     * @Route("/search/new", name="search_new")
     */
    public function new(
        Breadcrumb $breadcrumb,
        Request $request
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $search = new Search();

        $form = $this->createFormForSearch($search);

        $form->handleRequest($request);

        $breadcrumb->add('All Searches', $this->generateUrl('search'));

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($search);
            $entityManager->flush();

            return $this->redirectToRoute(
                'search_show',
                [
                    'id' => $search->getId(),
                ]
            );
        }

        $breadcrumb->setPageTitle('New Search');

        return $this->render(
            'search/form.html.twig',
            [
                'form' => $form->createView(),
                'breadcrumb' => $breadcrumb->get(),
            ]
        );
    }

    /**
     * Creates a form for the given Search.
     *
     * @param Search $search
     */
    public function createFormForSearch(Search $search): Form
    {
        return $this->createFormBuilder($search)
        ->add('title', TextType::class)
        ->add('save', SubmitType::class, ['label' => 'Save Search', 'attr' => ['class' => 'button']])
        ->getForm();
    }
}
