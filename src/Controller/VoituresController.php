<?php

namespace App\Controller;

use App\Entity\Voitures;
use App\Form\VoituresType;
use App\Repository\VoituresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/voitures')]
final class VoituresController extends AbstractController
{
    #[Route(name: 'app_voitures_index', methods: ['GET'])]
    public function index(VoituresRepository $voituresRepository): Response
    {
        return $this->render('voitures/index.html.twig', [
            'voitures' => $voituresRepository->findAll(),
        ]);
    }

    #[Route('/select', name: 'app_voitures_select', methods: ['GET'])]
    public function selectionVoitures(VoituresRepository $voituresRepository, Request $request): Response
    {
        // Récupérer les filtres
        $marques = $voituresRepository->findDistinctMarques();
        $boites = $voituresRepository->findDistinctBoites();
        $carburants = $voituresRepository->findDistinctCarburant();

        // Appliquer les filtres pour récupérer les voitures uniques
        $criteria = [
            'marque' => $request->query->get('marque', null),
            'boite' => $request->query->get('boite', null),
            'carburant' => $request->query->get('carburant', null),
        ];

        // Récupérer les voitures filtrées
        $voitures = $voituresRepository->findUniqueVoitures($criteria);

        $totalVoitures = $voituresRepository->count([]);

        return $this->render('voitures/select.html.twig', [
            'voitures' => $voitures,
            'marques' => $marques,
            'boites' => $boites,
            'carburants' => $carburants,
            'selectedMarque' => $criteria['marque'],
            'selectedBoite' => $criteria['boite'],
            'selectedCarburant' => $criteria['carburant'],
            'totalVoitures' => $totalVoitures,
        ]);
    }


    #[Route('/new', name: 'app_voitures_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $voiture = new Voitures();
        $form = $this->createForm(VoituresType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($voiture);
            $entityManager->flush();

            return $this->redirectToRoute('app_voitures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voitures/new.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_voitures_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Voitures $voiture): Response
    {
        return $this->render('voitures/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_voitures_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Voitures $voiture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoituresType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_voitures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voitures/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_voitures_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Voitures $voiture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $voiture->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($voiture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_voitures_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/voitures/{id}/duplicate', name: 'app_voitures_duplicate', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function duplicate(int $id, VoituresRepository $voituresRepository, EntityManagerInterface $entityManager): Response
    {
        $voiture = $voituresRepository->find($id);

        if (!$voiture) {
            throw $this->createNotFoundException('La voiture n\'existe pas.');
        }

        $duplicatedVoiture = clone $voiture;

        $entityManager->detach($duplicatedVoiture);

        $entityManager->persist($duplicatedVoiture);
        $entityManager->flush();

        return $this->redirectToRoute('app_voitures_index');
    }

    #[Route('/load-more', name: 'load_more_voitures', methods: ['GET'])]
    public function loadMoreVoitures(Request $request, VoituresRepository $voituresRepository): JsonResponse
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = 3;

        $voitures = $voituresRepository->findBy([], null, $limit, $offset);

        $totalVoitures = $voituresRepository->count([]);

        $data = [];
        foreach ($voitures as $voiture) {
            $data[] = [
                'id' => $voiture->getId(),
                'marque' => $voiture->getMarque(),
                'modele' => $voiture->getModele(),
                'image' => $voiture->getImage(),
                'annee' => $voiture->getAnnee(),
                'couleur' => $voiture->getCouleur(),
                'boite' => $voiture->getBoite(),
                'carburant' => $voiture->getCarburant(),
                'prix' => $voiture->getPrix(),
            ];
        }

        $hasMore = ($offset + count($voitures)) < $totalVoitures;

        return new JsonResponse([
            'voitures' => $data,
            'hasMore' => $hasMore,
        ]);
    }
}
