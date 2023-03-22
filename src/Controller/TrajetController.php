<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Reservation;
use App\Entity\Trajet;
use App\Form\CommentaireType;
use App\Form\TrajetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TrajetController extends AbstractController
{

    /**
     * Page d'accueil
     * @Route("/", name="home")
     * @return Response
     */
    public function home(): Response
    {
        return $this->render('trajet/home.html.twig');
    }

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
        $userReservation = $this->getDoctrine()
            ->getRepository(Reservation::class)
            ->findOneBy(['trajet' => $trajet, 'passager' => $this->getUser()]);

        $userHasReservation = $userReservation !== null;
        $tripDatePassed = $trajet->getDateDepart() <= new \DateTime();

        $form = $this->createForm(CommentaireType::class);

        return $this->render('trajet/show.html.twig', [
            'trajet' => $trajet,
            'form' => $form->createView(),
            'userHasReservation' => $userHasReservation,
            'tripDatePassed' => $tripDatePassed,
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


        if (!$this->getUser()) {
            // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

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

    /**
     * @Route("/trajet/{id}/comment", name="trajet_add_comment", methods={"POST"})
     */
    public function addComment(Trajet $trajet, Request $request, UserInterface $user): Response
    {
        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $comment->setTrajet($trajet);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('trajet.show', ['id' => $trajet->getId()]);
        }

        return $this->render('trajet/show.html.twig', [
            'trajet' => $trajet,
            'comment_form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/trajet/{id}/comment", name="post_comment", methods={"POST"})
     */
    public function postComment(Request $request, Trajet $trajet): Response
    {
        // Check if the user has a reservation for the trip
        $userReservation = $this->getDoctrine()
            ->getRepository(Reservation::class)
            ->findOneBy(['trajet' => $trajet, 'passager' => $this->getUser()]);

        if (!$userReservation) {
            $this->addFlash('error', 'Vous ne pouvez pas commenter un trajet que vous n\'avez pas réservé.');
            return $this->redirectToRoute('trajet.show', ['id' => $trajet->getId()]);
        }

        // Check if the trip date has passed
        if ($trajet->getDateDepart() > new \DateTime()) {
            $this->addFlash('error', 'Vous ne pouvez commenter un trajet qu\'après sa date.');
            return $this->redirectToRoute('trajet.show', ['id' => $trajet->getId()]);
        }

        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setTrajet($trajet);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');

            return $this->redirectToRoute('trajet.show', ['id' => $trajet->getId()]);
        }

        return $this->render('trajet/show.html.twig', [
            'trajet' => $trajet,
            'form' => $form->createView(),
        ]);
    }





}
