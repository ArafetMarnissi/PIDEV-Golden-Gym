<?php

namespace App\Controller;

use App\Repository\LigneCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

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

    ///////////////////////**************api*************//////////////////////////////////////////
    #[Route('/Apicommande/detais/{id}', name: 'ApidetaisCommandeClient')]
    public function afficherDetailsCommande($id,LigneCommandeRepository $ligneCommandeRepository,SerializerInterface $serializer): Response
    {
         $ligneCommande = $ligneCommandeRepository->findByCommandeId($id);
        foreach ($ligneCommande as $LC) {
            $ListLigneCommande[] = [
                'id' => $LC->getid(),
                'nomProduit' => $LC->getProduits()->getNom(),
                'QuProduit' => $LC->getQuantiteProduit(),
                'PrixUnitaire'=>$LC->getPrixUnitaire()
            ];
            
        }
        $json = $serializer->serialize($ListLigneCommande, 'json');
        
        // $commandesNormalises=$normalizerInterface->normalize($ligneCommande,'json',['groups' => 'commandes']);
         return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }




    // public function creerListeCommandes(array $commandes, SerializerInterface $serializer): Response
    // {
    //     $listeCommandes = [];

    //     foreach ($commandes as $c) {
    //         $commande = new Commande();
    //         $commande->nomProduit = $c->produit;
    //         $commande->quantiteProduit = $c->quantiteProduit;
    //         $commande->prixUnitaire = $c->PrixUnitaire;

    //         $listeCommandes[] = $commande;
    //     }

    //     $json = $serializer->serialize($listeCommandes, 'json');

    //     return new Response($json, 200, [
    //         'Content-Type' => 'application/json'
    //     ]);
    // }
}


