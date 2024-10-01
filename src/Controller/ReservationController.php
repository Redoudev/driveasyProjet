<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Voitures;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\VoituresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/resa/{id}', name: 'app_reservation_resa', methods: ['GET', 'POST'])]
    public function reservation(int $id, VoituresRepository $voituresRepository, ReservationRepository $reservationRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $voiture = $voituresRepository->find($id);
        $user = $this->getUser();
        $reservation = new Reservation(); // la je cree une new instance
        // Pré-remplisage
        $reservation->setVoiture($voiture);
        $reservation->setAgence($voiture->getAgence());
        $reservation->setUser($user);
        //
        $form = $this->createForm(ReservationType::class, $reservation); // la je creer un form et je mets les info de mon instance vide ou non $reservation qui vont être rempli
        $form->handleRequest($request); // je charge tout les infos rempli en post

        if ($form->isSubmitted() && $form->isValid()) {
            $dateDepart = $reservation->getDateDepart();
            $dateRetour = $reservation->getDateRetour();
            
            $existReservation = $reservationRepository->conflictReservations($voiture, $dateDepart, $dateRetour);

            // Condition multiple résa sur meme période + Message d'erreur
            if (count($existReservation) > 0 || ($dateRetour < $dateDepart)) {
                $this->addFlash('error', 'La voiture est déjà réservée pour cette période.');
            } else {
                $entityManager->persist($reservation); // Si tout s'est bien passé let's go sa envoi en bdd
                $entityManager->flush();

                return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('reservation/resa.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
