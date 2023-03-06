<?php

namespace App\Controller;

use App\Repository\LigneCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LigneCommandeController extends AbstractController
{
    #[Route('/ligne/commande/{id}', name: 'app_ligne_commande')]
    public function afficher($id,LigneCommandeRepository $ligneCommandeRepository): Response
    {
         $ligneCommande = $ligneCommandeRepository->findByCommandeId($id);
        
        
        return $this->render('ligne_commande/LigneCommandeProduit.html.twig', [
            'controller_name' => 'LigneCommandeController',
            'ligneCommande' => $ligneCommande,
        ]);
    }
    #[Route('/commande/detais/{id}', name: 'detaisCommandeClient')]
    public function afficherClient($id,LigneCommandeRepository $ligneCommandeRepository): Response
    {
         $ligneCommande = $ligneCommandeRepository->findByCommandeId($id);
        
        
        return $this->render('ligne_commande/LigneCommandeClient.html.twig', [
            'controller_name' => 'LigneCommandeController',
            'ligneCommande' => $ligneCommande,
        ]);
    }
}
