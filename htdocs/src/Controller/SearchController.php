<?php

namespace App\Controller;

use App\Entity\PropertyType;
use App\Entity\Search;
use App\Form\Type\MapType;
use App\Service\Breadcrumb;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchController extends AbstractController
{
    /** @var float DEFAULT_LATITUDE Faro Latitude */
    /** @var float DEFAULT_LONGITUDE Faro Longitude */
    private const DEFAULT_LATITUDE = 37.019356;
    private const DEFAULT_LONGITUDE = -7.930440;

    /**
     * @Route("/search", name="search")
     * @param Breadcrumb $breadcrumb
     *
     * @return Response
     */
    public function index(
        Breadcrumb $breadcrumb
    ) : Response {
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
     * @param Search     $search
     * @param Breadcrumb $breadcrumb
     *
     * @return Response
     */
    public function show(
        Search $search,
        Breadcrumb $breadcrumb
    ) : Response
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
     * @param Breadcrumb $breadcrumb
     * @param Request $request
     *
     * @return RedirectResponse|Response
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

            /** @var Search $search */
            $search = $this->setConfigurationField($search);

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
     * @Route("/search_edit/{id}", name="search_edit", requirements={"id"="\d+"})
     * @param Search $search
     * @param Request $request
     * @param Breadcrumb $breadcrumb
     *
     * @return RedirectResponse|Response
     */
    public function edit(
        Search $search,
        Request $request,
        Breadcrumb $breadcrumb
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$search) {
            throw $this->createNotFoundException('No search found');
        }

        /** @var  $form */
        $form = $this->createFormForSearch($search);

        $breadcrumb->setPageTitle($search->getTitle());
        $breadcrumb->add('All Searches', $this->generateUrl('search'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($search);
            $entityManager->flush();

            return $this->redirectToRoute(
                'search_show',
                [
                    'id' => $search->getId(),
                    'breadcrumb' => $breadcrumb->get(),
                ]
            );
        }

        return $this->render(
            'search/form.html.twig',
            [
                'form' => $form->createView(),
                'breadcrumb' => $breadcrumb->get(),
            ]
        );
    }

    /**
     * @param Search $search
     * @return Search
     */
    private function setConfigurationField(Search $search) : Search
    {
        $search->setConfiguration('{}');
        return $search;
    }

    /**
     * Creates a form for the given Search.
     *
     * @param Search $search
     * @return FormInterface
     */
    public function createFormForSearch(Search $search): FormInterface
    {
        /** @var FormInterface $form */
        $form = $this->createFormBuilder($search)
            ->add('title', TextType::class)
            ->add('property_type', EntityType::class,
                [
                    'class' => PropertyType::class,
                    'choice_label' => 'title'
                ])
            ->add('coordinates', MapType::class,
                ['data' =>   [
                    'latitude'  => self::DEFAULT_LATITUDE,
                    'longitude' => self::DEFAULT_LONGITUDE
                ]
                ])
            ->add('radius', IntegerType::class,
                ['data' => 1000])
            ->add('min_price', MoneyType::class)
            ->add('max_price', MoneyType::class)
            ->add('save', SubmitType::class, ['label' => 'Save Search', 'attr' => ['class' => 'button']])
            ->getForm();

        return $form;
    }
}
