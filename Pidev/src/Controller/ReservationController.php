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
use App\Entity\Reservation;
use App\Form\ReservationType;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/indexr.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }

    #[Route('/listAb', name: 'list_AbonnementC')]
    public function list(ManagerRegistry $doctrine): Response 
    {
        $repository= $doctrine->getRepository(Abonnement::class);
        $abonnements=$repository->FindAll(); 

        return $this->render('reservation/indexr.html.twig', [
            'abonnements' => $abonnements,

        ]);

    }


    #[Route('/listR', name: 'list_reservation')]
    public function list1(ManagerRegistry $doctrine): Response 
    {
        $repository= $doctrine->getRepository(Reservation::class);
        $reservations=$repository->FindAll(); 

        return $this->render('reservation/affiche.html.twig', [
            'reservations' => $reservations,

        ]);

    }


    #[Route('/newr/{id}', name: 'reservation_new')]
    public function newr(Request $request,$id,ManagerRegistry $doctrine): Response
{
    $reservation = new Reservation();
    $abonnement= new Abonnement();
    $repository= $doctrine->getRepository(Abonnement::class);
    $abonnement=$repository->find($id);
    $reservation->setReservationAbonnement($abonnement);

    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);
    $date = new \DateTime('@'.strtotime('now'));
    $datefm = new \DateTime('@'.strtotime('now +30 days'));
    $datefa = new \DateTime('@'.strtotime('now +365 days'));

    if ($form->isSubmitted() && $form->isValid()) {
        $reservation->setDateDebut($date);
        if($abonnement->getDureeAbonnement()== "Annuel"){$reservation->setDateFin($datefa);}
        if($abonnement->getDureeAbonnement()== "Mensuel"){$reservation->setDateFin($datefm);}    
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('list_reservation');
    }

    return $this->render('reservation/newr.html.twig', [
        'form' => $form->createView()
    ]);
}


#[Route('/deleter/{id}',name:'delete_une_reservation')]
public function deleter (ManagerRegistry $doctrine, $id):Response
{   $repository=$doctrine->getRepository(Reservation::class);
    $reservations=$repository->find($id);
    $em= $doctrine->getManager();
    $em->remove($reservations);
    $em->flush();
    return $this->redirectToRoute('list_reservation');
}












}
