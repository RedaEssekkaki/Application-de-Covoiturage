<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Trajet;
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
    public function list(): Response
    {
        $user = $this->security->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findAll();
        } else {
            $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findBy(['passager' => $user]);
        }

        return $this->render('reservation/list.html.twig', [
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
