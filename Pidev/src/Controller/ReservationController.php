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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ReservationRepository;

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
        $repo= $doctrine->getRepository(Reservation::class);
        $abonnements=$repository->FindAll(); 
        $reservationss=$repo->FindAll();

        return $this->render('reservation/indexr.html.twig', [
            'abonnements' => $abonnements, 'reservationss' => $reservationss,

        ]);

    }


    #[Route('/listR', name: 'list_reservation')]
    public function list1(ReservationRepository $rep ): Response 
    {
        
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
            $idu = $user->getId();
        }
        $reservations=$rep->findByUserId($idu);   

        return $this->render('reservation/affiche.html.twig', [
            'reservations' => $reservations,

        ]);

    }


#[Route('/newr/{id}', name: 'reservation_new')]
public function newr(Request $request, $id, ManagerRegistry $doctrine, UserRepository $userRepository, SessionInterface $session,ReservationRepository $rep): Response
{
    $user = $this->getUser();
    if ($user instanceof \App\Entity\User) {
        $idu = $user->getId();
    }
    $reservation = new Reservation();
    $abonnement = new Abonnement();
    $repository = $doctrine->getRepository(Abonnement::class);
    $repo = $doctrine->getRepository(Reservation::class);
    $abonnement = $repository->find($id);
    $reservation->setReservationAbonnement($abonnement);
    $reservations = $repo->find($idu);
    $rep = $doctrine->getRepository(Reservation::class)->findOneBy([
        'user' => $reservation->getUser(),
        'ReservationAbonnement' => $reservation->getReservationAbonnement(),
    ]);

    $user = $this->getUser();
    if ($user instanceof \App\Entity\User) {
        $idu = $user->getId();
    }
    $reservation->setUser($userRepository->find($idu));

    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);
    $date = new \DateTime('@' . strtotime('now'));
    $datefm = new \DateTime('@' . strtotime('now +1 months'));
    $datefa = new \DateTime('@' . strtotime('now +1 years'));

    $hasReservation = ($rep !== null);

    if ($form->isSubmitted() && $form->isValid()) {
        $reservation->setDateDebut($date);
        if ($abonnement->getDureeAbonnement() == "Annuel") {
            $reservation->setDateFin($datefa);
        }
        if ($abonnement->getDureeAbonnement() == "Mensuel") {
            $reservation->setDateFin($datefm);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();
        $this->addFlash('success', 'La réservation a été ajoutée avec succès.');

        return $this->redirectToRoute('list_reservation');
    }

    return $this->render('reservation/newr.html.twig', [
        'form' => $form->createView(),
        'reservations' => $reservations,
        'hasReservation' => $hasReservation,
        'rep' => $rep,
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
