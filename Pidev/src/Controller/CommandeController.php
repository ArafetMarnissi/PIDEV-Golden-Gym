<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function  add(ManagerRegistry $doctrine, Request  $request): Response
    {
        $commande = new commande();
        $form = $this->createForm(commandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($commande);
            $em->flush();


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
