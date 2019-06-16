<?php

namespace App\Controller;

use App\Entity\Property;
use App\Service\Memcached;
use App\Service\Breadcrumb;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PropertyController extends AbstractController
{
    /**
     * @Route("/property", name="property")
     */
    public function index(
        Breadcrumb $breadcrumb,
        LoggerInterface $logger,
        Memcached $memcached
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $properties = $this->getDoctrine()->getRepository(Property::class)->findAll();
        $breadcrumb->setPageTitle('All Properties');

        return $this->render('property/index.html.twig', [
            'controller_name' => 'PropertyController',
            'pageTitle' => 'All Properties',
            'properties' => $properties,
            'breadcrumb' => $breadcrumb->get(),
        ]);
    }

    /**
     * @Route("/property/{id}", name="property_show", requirements={"id"="\d+"})
     */
    public function show(Property $property, Breadcrumb $breadcrumb)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$property) {
            throw $this->createNotFoundException('No property found for id '.$id);
        }

        $breadcrumb->setPageTitle($property->getTitle());
        $breadcrumb->add('All Properties', $this->generateUrl('property'));

        return $this->render(
            'property/show.html.twig',
            [
                'property' => $property,
                'breadcrumb' => $breadcrumb->get(),
            ]
        );
    }

    /**
     * @Route("/property_edit/{id}", name="property_edit", requirements={"id"="\d+"})
     * @param Property $property
     * @param Request $request
     * @param Breadcrumb $breadcrumb
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(
        Property $property,
        Request $request,
        Breadcrumb $breadcrumb
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$property) {
            throw $this->createNotFoundException("No property found");
        }

        $form = $this->createFormForProperty($property);

        $breadcrumb->setPageTitle($property->getTitle());
        $breadcrumb->add('All Properties', $this->generateUrl("property"));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $property = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($property);
            $entityManager->flush();

            return $this->redirectToRoute(
                'property_show',
                [
                    'id' => $property->getId(),
                    'breadcrumb' => $breadcrumb->get(),
                ]
            );
        }

        return $this->render(
            'property/form.html.twig',
            [
                'form' => $form->createView(),
                'breadcrumb' => $breadcrumb->get(),
            ]
        );
    }

    /**
     * @Route("/property/new", name="property_new")
     */
    public function new(
        Breadcrumb $breadcrumb,
        Request $request
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $property = new Property();

        $form = $this->createFormForProperty($property);

        $form->handleRequest($request);

        $breadcrumb->add('All Properties', $this->generateUrl('property'));

        if ($form->isSubmitted() && $form->isValid()) {
            $property = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($property);
            $entityManager->flush();

            return $this->redirectToRoute(
                'property_show',
                [
                    'id' => $property->getId(),
                ]
            );
        }

        $breadcrumb->setPageTitle('New Property');

        return $this->render(
            'property/form.html.twig',
            [
                'form' => $form->createView(),
                'breadcrumb' => $breadcrumb->get(),
            ]
        );
    }

    /**
     * Creates a form for the given Property.
     *
     * @param Property $property
     */
    public function createFormForProperty(Property $property): Form
    {
        return $this->createFormBuilder($property)
        ->add('title', TextType::class)
        ->add('save', SubmitType::class, ['label' => 'Save Property', 'attr' => ['class' => 'button']])
        ->getForm();
    }
}
