<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AbonnementType;
use App\Entity\Abonnement;
use App\Controller\SubmitType;
use Doctrine\Persistence\ManagerRegistry;

class AbonnementController extends AbstractController
{
    #[Route('/abonnement', name: 'abonnement_index' )]
    public function index(): Response
    {
        return $this->render('abonnement/index.html.twig', [
            'controller_name' => 'AbonnementController',
        ]);
    }

    #[Route('/new', name: 'abonnement_new')]
    public function new(Request $request): Response
{
    $abonnement = new Abonnement();
    $form = $this->createForm(AbonnementType::class, $abonnement);
    $form->handleRequest($request);
    

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($abonnement);
        $entityManager->flush();

        return $this->redirectToRoute('list_Abonnement');
    }

    return $this->render('abonnement/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/listA', name: 'list_Abonnement')]
public function list(ManagerRegistry $doctrine): Response 
{
    $repository= $doctrine->getRepository(Abonnement::class);
    $abonnements=$repository->FindAll(); 

<<<<<<< HEAD
=======
    
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
    return $this->render('abonnement/index.html.twig', [
        'abonnements' => $abonnements,

    ]);

}

#[Route('/updateAbonnement/{id}', name: 'modifier_un_abonnement')]
public function update(ManagerRegistry $doctrine,Request $request, $id): Response
{
    $repository= $doctrine->getRepository(Abonnement::class);
    $abonnements=$repository->find($id);
    $form= $this->createForm(AbonnementType::class, $abonnements );
    $form->handleRequest($request);
    if($form->isSubmitted()){
        $em= $doctrine->getManager();
        $em->persist($abonnements);
        $em->flush();
        return $this->redirectToRoute('list_Abonnement'); 
    }
    return $this->render('abonnement/new.html.twig', [ 'form' => $form->createView()
]); 
}

#[Route('/delete/{id}',name:'delete_un_abonnement')]
public function delete (ManagerRegistry $doctrine, $id):Response
{   $repository=$doctrine->getRepository(Abonnement::class);
    $abonnement=$repository->find($id);
    $em= $doctrine->getManager();
    $em->remove($abonnement);
    $em->flush();
    return $this->redirectToRoute('list_Abonnement');
}

} 
