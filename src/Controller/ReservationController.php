<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Voitures;
use App\Form\ReservationType;
use App\Form\UserReservationType;
use App\Repository\ReservationRepository;
use App\Repository\VoituresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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
    public function reservation(
        int $id,
        VoituresRepository $voituresRepository,
        ReservationRepository $reservationRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $voiture = $voituresRepository->find($id);
        $user = $this->getUser();

        // Vérification si l'utilisateur est connecté
        if (!$user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'error' => true,
                    'message' => 'Vous devez être connecté pour réserver une voiture.',
                ], 401);
            }

            return $this->redirectToRoute('app_login');
        }

        $reservation = new Reservation();
        $reservation->setVoiture($voiture);
        $reservation->setUser($user);

        // Création du formulaire avec l'agence sélectionnée
        $form = $this->createForm(UserReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dateDepart = $reservation->getDateDepart();
            $dateRetour = $reservation->getDateRetour();
            $agence = $reservation->getAgence(); // Récupération de l'agence sélectionnée dans le formulaire

            // Vérification des conflits avec la voiture et l'agence sélectionnée
            $existReservation = $reservationRepository->conflictReservations($voiture, $dateDepart, $dateRetour, $agence);

            if (count($existReservation) > 0 || ($dateRetour < $dateDepart)) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse([
                        'error' => true,
                        'message' => 'La voiture est déjà réservée pour cette période dans cette agence.',
                    ], 400);
                }
                $this->addFlash('error', 'La voiture est déjà réservée pour cette période dans cette agence.');
            } else {
                $entityManager->persist($reservation);
                $entityManager->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse([
                        'error' => false,
                        'message' => 'Votre réservation a bien été enregistrée.',
                    ]);
                }

                return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('reservation/resa.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
            'voiture' => $voiture,
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
