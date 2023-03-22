<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Form\TrajetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrajetController extends AbstractController
{


    /**
     * Afficher la liste des trajets
     * @Route("/trajet", name="trajet.list")
     * * @return Response
     */
    public function list(): Response
    {
        $trajets = $this->getDoctrine()->getRepository(Trajet::class)->findAll();
        return $this->render('trajet/list.html.twig', [
            'trajets' => $trajets,
        ]);
    }

    /**
     * Chercher et afficher un trajet
     * @Route("/trajet/{id}", name = "trajet.show", requirements = {"id" = "\d+"})
     * @param Trajet $trajet
     * @return Response
     * */
    public function show(Trajet $trajet) : Response
    {
        return $this->render('trajet/show.html.twig', [
            'trajet' => $trajet
        ]);
    }


    /**
     * Créer un nouveau trajet
     * @Route("/trajet/new", name="trajet.create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $trajet = new Trajet();
        $form = $this->createForm(TrajetType::class, $trajet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trajet);
            $em->flush();

            return $this->redirectToRoute('trajet.show', ['id' => $trajet->getId()]);
        }

        return $this->render('trajet/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Supprimer un trajet
     * @Route("/trajet/{id}/delete", name="trajet.delete", requirements={"id"="\d+"})
     * @param Trajet $trajet
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Trajet $trajet, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class, [
                'label' => 'Oui, Supprimer',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($trajet);
            $entityManager->flush();
            $this->addFlash('success', 'Le trajet a été supprimé avec succès.');
            return $this->redirectToRoute('trajet.list');
        }

        return $this->render('trajet/delete.html.twig', [
            'trajet' => $trajet,
            'form' => $form->createView()
        ]);
    }

    /**
     * Editer un trajet
     * @Route("/trajet/{id}/edit", name="trajet.edit", requirements={"id"="\d+"})
     * @param Trajet $trajet
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Trajet $trajet, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrajetType::class, $trajet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Trajet modifié avec succès');
            return $this->redirectToRoute('trajet.show', ['id' => $trajet->getId()]);
        }

        return $this->render('trajet/edit.html.twig', [
            'trajet' => $trajet,
            'form' => $form->createView()
        ]);
    }



}
