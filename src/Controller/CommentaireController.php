<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Reservation;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/commentaire/add/{reservationId}", name="add_comment")
     */
    public function add(Request $request, int $reservationId): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Recherchez la réservation en fonction de l'ID de réservation
            $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);
            $trajet = $reservation->getTrajet();

            $commentaire->setUser($this->getUser());
            $commentaire->setTrajet($trajet);

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('reservation.list');
        }

        return $this->render('commentaire/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
