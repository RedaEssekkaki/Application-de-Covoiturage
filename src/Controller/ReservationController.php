<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Trajet;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ReservationController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * Afficher la liste des réservations
     * @Route("/reservation", name="reservation.list")
     * @return Response
     */
    public function list(ReservationRepository $reservationRepository): Response
    {
        $user = $this->security->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $reservations = $reservationRepository->findNonExpired();
        } else {
            $reservations = $reservationRepository->findNonExpiredByUser($user);
        }

        return $this->render('reservation/list.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Afficher l'historique des réservations pour un utilisateur
     * @Route("/reservation/history", name="reservation.history")
     * @return Response
     */
    public function history(ReservationRepository $reservationRepository): Response
    {
        $user = $this->security->getUser();
        $reservations = $reservationRepository->findAllByUser($user);

        return $this->render('reservation/history.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    /**
     * Créer une nouvelle réservation
     * @Route("/reservation/new/{trajet_id}", name="reservation.create", requirements={"trajet_id"="\d+"})
     * @ParamConverter("trajet", options={"id"="trajet_id"})
     * @param Trajet $trajet
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function create(Trajet $trajet, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

        // Vérifie si l'utilisateur connecté est le conducteur du trajet
        if ($user === $trajet->getConducteur()) {
            // Ajoute un message d'erreur
            $this->addFlash('error', "Vous ne pouvez pas réserver votre propre trajet.");

            // Redirige vers la page précédente ou une autre page de votre choix
            return $this->redirectToRoute('trajet.list');
        }

        $nombrePlacesDisponibles = $trajet->getNombrePlaces();

        if ($nombrePlacesDisponibles > 0) {
            $reservation = new Reservation();
            $reservation->setPassager($this->getUser());
            $reservation->setTrajet($trajet);

            // Met à jour le nombre de places disponibles pour le trajet
            $trajet->setNombrePlaces($nombrePlacesDisponibles - 1);

            // Enregistre la réservation et met à jour le trajet
            $entityManager->persist($reservation);
            $entityManager->flush();

            // Ajoute un message de confirmation
            $this->addFlash('success', 'Réservation effectuée avec succès.');

            // Redirige vers la page de détails de la réservation
            return $this->redirectToRoute('reservation.show', ['id' => $reservation->getId()]);
        } else {
            // Ajoute un message d'erreur si aucune place n'est disponible
            $this->addFlash('error', 'Aucune place disponible pour ce trajet.');

            // Redirige vers la page précédente ou une autre page de votre choix
            return $this->redirectToRoute('trajet.list');
        }
    }


    /**
     * Chercher et afficher une réservation
     * @Route("/reservation/{id}", name="reservation.show", requirements={"id"="\d+"})
     * @param Reservation $reservation
     * @return Response
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation
        ]);
    }


    /**
     * Supprimer une réservation
     * @Route("/reservation/{id}/delete", name="reservation.delete", requirements={"id"="\d+"})
     * @param Reservation $reservation
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Reservation $reservation, Request $request, EntityManagerInterface $entityManager): Response
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
            // Récupère l'objet Trajet associé à la réservation
            $trajet = $reservation->getTrajet();

            // Augmente le nombre de places disponibles pour le trajet
            $trajet->setNombrePlaces($trajet->getNombrePlaces() + 1);

            // Supprime la réservation
            $entityManager->remove($reservation);
            $entityManager->flush();
            $this->addFlash('success', 'La réservation a été supprimée avec succès.');
            return $this->redirectToRoute('reservation.list');
        }

        return $this->render('reservation/delete.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView()
        ]);
    }





}
