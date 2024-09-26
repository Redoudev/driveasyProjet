<?php

namespace App\Controller;

use App\Repository\VoituresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VoituresController extends AbstractController
{
    #[Route('/voiture', name: 'app_voiture_selection')]
    public function voitureSelection(VoituresRepository $voituresRepository): Response
    {
        $voitures = $voituresRepository->findAll();
        return $this->render('voiture/select.html.twig', [
            'voitures' => $voitures,
            'controller_name' => 'VoitureController',
        ]);
    }
}
