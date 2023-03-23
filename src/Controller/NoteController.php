<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    /**
     * @Route("/note/add/{trajetId}", name="note_add", methods={"POST"})
     */
    public function addNote(Request $request, int $trajetId, EntityManagerInterface $em, ReservationRepository $reservationRepository, NoteRepository $noteRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Trouver la réservation correspondante
        $reservation = $reservationRepository->findOneBy(['trajet' => $trajetId, 'passager' => $user]);

        // Vérifier si une note existe déjà pour cette réservation
        $existingNote = $noteRepository->findOneBy(['conducteur' => $reservation->getTrajet()->getConducteur(), 'passager' => $user]);

        if ($existingNote) {
            $this->addFlash('error', 'Vous avez déjà ajouté une note pour ce trajet.');
            return $this->redirectToRoute('reservation.history');
        }

        // Récupérer les données du formulaire (valeur de la note et commentaire)
        $valeur = $request->get('valeur');
        $commentaire = $request->get('commentaire');

        // Créer une nouvelle instance de Note et définir les propriétés
        $note = new Note();
        $note->setValeur($valeur);
        $note->setCommentaire($commentaire);
        $note->setPassager($user);
        $note->setConducteur($reservation->getTrajet()->getConducteur());

        // Enregistrer la note dans la base de données
        $em->persist($note);
        $em->flush();

        // Ajouter un message flash de succès et rediriger vers la page d'historique des réservations
        $this->addFlash('success', 'Note ajoutée avec succès.');
        return $this->redirectToRoute('reservation.history');
    }
}
