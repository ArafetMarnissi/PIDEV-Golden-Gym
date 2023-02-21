<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\User;
use App\Form\CommandeType;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommandeController extends AbstractController
{

    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    #[Route('/ajouterCommande', name: 'ajouter_commande')]
    public function  add(ManagerRegistry $doctrine, Request  $request, UserRepository $userRepository,SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $commande = new commande();
        //recupérer l id de utilisateur connecté
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
        $id = $user->getId();
       }
       // ajouter l utilisateur a la commande
        $commande->setUser($userRepository->find($id));
        

        $form = $this->createForm(commandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($commande);
            $em->flush();
            
            //parcourir le panier, pour chaque element du panier est instancié un objet ligne commande  

            $panier = $session->get('panier', []);
            foreach ($panier as $id => $quantity) {
               
                
                $LingeCommande= new LigneCommande();
                $LingeCommande->setCommande($commande);
                $LingeCommande->setProduits($produitRepository->find($id));
                $LingeCommande->setPrixUnitaire($produitRepository->find($id)->getPrixProduit());
                $LingeCommande->setQuantiteProduit($quantity);
                $em->persist($LingeCommande);
                $em->flush();
                unset($panier[$id]);
                $session->set('panier', $panier);

            }
           

            return $this->redirectToRoute('Affichagecommande');
        }
        return $this->renderForm(
            "commande/add_edit_Commande.html.twig",
            ["formCommande" => $form, "editMode" => $commande->getId() !== null]
        );
    }

    #[Route('/affichagecommande', name: 'Affichagecommande')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(commande::class);
        $commandes = $repository->findAll();
        return $this->render('commande/AffichageCommande.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/updatecommande/{id}', name: 'modifier_commande')]
    public function  update(ManagerRegistry $doctrine, $id,  Request  $request): Response
    {
        $commande = $doctrine
            ->getRepository(commande::class)
            ->find($id);
        $form = $this->createForm(commandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('Affichagecommande');
        }
        return $this->renderForm(
            "commande/add_edit_Commande.html.twig",
            [
                "formCommande" => $form,
                "editMode" => $commande->getId() !== null
            ]
        );
    }

    #[Route('/deletecommande/{id}', name: 'supprimer_commande')]
    public function DeleteS(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(commande::class);
        $commande = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($commande);
        $em->flush();
        return $this->redirectToRoute('Affichagecommande');
    }

  
}
