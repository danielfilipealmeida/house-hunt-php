<?php

namespace App\Controller;

use App\Entity\SearchResult;
use App\Service\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SearchResultController extends AbstractController
{
    /**
     * @Route("/searchresult", name="search_result")
     */
    public function index(
        Breadcrumb $breadcrumb
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $searchResults = $this->getDoctrine()
            ->getRepository(SearchResult::class)
            ->findBy(
                array(),
                array('id' => 'DESC')
            );
        $breadcrumb->setPageTitle('All Search Results');

        return $this->render('search_result/index.html.twig', [
            'controller_name' => 'SearchResultController',
            'pageTitle' => 'All Search Results',
            'searchResults' => $searchResults,
            'breadcrumb' => $breadcrumb->get(),
        ]);
    }

    /**
     * @Route("/searchresult_show/{id}", name="show_search_result", requirements={"id"="\d+"})
     */
    public function show(
        SearchResult $searchResult,
        Breadcrumb $breadcrumb
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $breadcrumb->setPageTitle($searchResult->getSearch());
        $breadcrumb->add('All Search Results', $this->generateUrl('search_result'));

        return $this->render(
            'search_result/show.html.twig',
            [
                'breadcrumb' => $breadcrumb->get(),
                'searchResult' => $searchResult,
            ]
        );
    }
}
