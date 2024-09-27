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
    public function selectionVoitures(VoituresRepository $voituresRepository): Response
    {
        $voitures = $voituresRepository->findAll();
        return $this->render('voitures/select.html.twig', [
            'voitures' => $voitures,
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

    #[Route('/{id}', name: 'app_voitures_show', methods: ['GET'])]
    public function show(Voitures $voiture): Response
    {
        return $this->render('voitures/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_voitures_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_voitures_delete', methods: ['POST'])]
    public function delete(Request $request, Voitures $voiture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voiture->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($voiture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_voitures_index', [], Response::HTTP_SEE_OTHER);
    }
}
