<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Form\LocationAddType;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/* TESTES */
use App\Repository\VehiculeRepository;

/**
 * @Route("/location")
 */
class LocationController extends AbstractController
{
    /**
     * @Route("/", name="location_index", methods={"GET"})
     */
    public function index(LocationRepository $locationRepository): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($user->getRoles() == ['ROLE_ADMIN']) {
            $locations = $locationRepository->findAll();
        }
        else if ($user->getRoles() == ['ROLE_USER']) {
            $locations = $locationRepository->findBy(['user' => $user->getId()]);
        }

        return $this->render('location/index.html.twig', [
            'locations' => $locations,
        ]);
    }

    /**
     * @Route("/new", name="location_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $location->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();

            return $this->redirectToRoute('location_index');
        }

        return $this->render('location/new.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/add/{id}", name="location_add", methods={"GET","POST"})
     */
    public function add(Request $request, int $id, VehiculeRepository $vehiculeRepository): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $vehicule = $vehiculeRepository->find($id);
        
        $location = new Location();
        $form = $this->createForm(LocationAddType::class, $location);
        $form->get('vehicule')->setData($vehicule);

        $contrat = $vehicule->getType()->getName();

        $form->get('contrat')->setData($vehicule->getType()->getName());

        var_dump($vehicule->getType()->getName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $location->setUser($user);
            $location->setVehicule($vehicule);
            $location->setStatus("activée");

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();

           return $this->redirectToRoute('location_index');
        }

        return $this->render('location/add.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
            'vehicule' => $vehicule,
        ]);
    }

    /**
     * @Route("/{id}", name="location_show", methods={"GET"})
     */
    public function show(Location $location): Response
    {
        return $this->render('location/show.html.twig', [
            'location' => $location,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="location_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Location $location): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('location_index', [
                'id' => $location->getId(),
            ]);
        }

        return $this->render('location/edit.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="location_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Location $location): Response
    {
        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($location);
            $entityManager->flush();
        }

        return $this->redirectToRoute('location_index');
    }
}
