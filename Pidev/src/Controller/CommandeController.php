<?php

namespace App\Controller;

use App\Entity\Commande;
<<<<<<< HEAD
use App\Form\CommandeType;
=======
use App\Entity\User;
use App\Form\CommandeType;
use App\Repository\UserRepository;
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
<<<<<<< HEAD
=======

>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    #[Route('/ajouterCommande', name: 'ajouter_commande')]
<<<<<<< HEAD
    public function  add(ManagerRegistry $doctrine, Request  $request): Response
    {
        $commande = new commande();
=======
    public function  add(ManagerRegistry $doctrine, Request  $request, UserRepository $userRepository): Response
    {
        $commande = new commande();
        //à changer
        $user = new user();
        $user = $userRepository->find(2);
        $commande->setUser($user);
        //
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
        $form = $this->createForm(commandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($commande);
            $em->flush();
<<<<<<< HEAD
=======
            //parcourir le panier, pour chaque element du panier est instancié un objet ligne commande  
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1


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
