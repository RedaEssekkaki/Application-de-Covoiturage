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
     * Créer une réservation pour un trajet
     * @Route("/trajet/{id}/reserver", name="reservation.create")
     * @param Trajet $trajet
     * @return Response
     */
    public function create(Trajet $trajet): Response
    {
        $user = $this->security->getUser();
        $reservation = new Reservation();
        $reservation->setPassager($user);
        $reservation->setTrajet($trajet);

        $em = $this->getDoctrine()->getManager();
        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Réservation effectuée avec succès.');

        return $this->redirectToRoute('reservation.list');
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
