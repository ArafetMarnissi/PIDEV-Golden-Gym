<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\ActiviteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActiviteController extends AbstractController
{
    // #[Route('/activite', name: 'app_activite')]
    // public function index(): Response
    // {
    //     return $this->render('activite/index.html.twig', [
    //         'controller_name' => 'ActiviteController',
    //     ]);
    // }

    #[Route('/addActivite', name: 'addActivite')]
    public function  add(ManagerRegistry $doctrine, Request  $request): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        #$form->add('ajouter', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($activite);
            $em->flush();


            return $this->redirectToRoute('AffichageActivite');
        }
        return $this->renderForm(
            "activite/index.html.twig",
            ["f" => $form]
        );
    }

    #[Route('/affichageActivite', name: 'AffichageActivite')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->findAll();
        return $this->render('activite/indexAffichage.html.twig', [
            'activites' => $Activites,
        ]);
    }

    #[Route('/updateActivite/{id}', name: 'updateActivite')]
    public function  update(ManagerRegistry $doctrine, $id,  Request  $request): Response
    {
        $activite = $doctrine
            ->getRepository(Activite::class)
            ->find($id);
        $form = $this->createForm(ActiviteType::class, $activite);
        #$form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('AffichageActivite');
        }
        return $this->renderForm(
            "activite/indexUpdate.html.twig",
            ["f" => $form]
        );
    }

    #[Route('/deleteActivite/{id}', name: 'deleteActivite')]
    public function DeleteS(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Activite::class);
        $activite = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($activite);
        $em->flush();
        return $this->redirectToRoute('AffichageActivite');
    }
}
