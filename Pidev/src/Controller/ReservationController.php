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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Repository\AbonnementRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime;
use Symfony\Component\Mime\MimeTypes;

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
public function newr(Request $request, $id, ManagerRegistry $doctrine, UserRepository $userRepository, SessionInterface $session,ReservationRepository $rep,ReservationRepository $reservationRepository): Response
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
    $reservations = $repo->findByUserId($idu);
    $rep = $doctrine->getRepository(Reservation::class)->findOneBy([
        'user' => $reservation->getUser(),
        'ReservationAbonnement' => $reservation->getReservationAbonnement(),
    ]);

    $user = $this->getUser();
    if ($user instanceof \App\Entity\User) {
        $idu = $user->getId();
        $nomClient= $user->getNom();
    }
    $reservation->setUser($userRepository->find($idu));

    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);
    $date = new \DateTime('@' . strtotime('now'));
    $datefm = new \DateTime('@' . strtotime('now +1 months'));
    $datefa = new \DateTime('@' . strtotime('now +1 years'));

    if ($form->isSubmitted() && $form->isValid()) {
        $reservation->setDateDebut($date);
        if ($abonnement->getDureeAbonnement() == "Annuel") {
            $reservation->setDateFin($datefa);
        }
        if ($abonnement->getDureeAbonnement() == "Mensuel") {
            $reservation->setDateFin($datefm);
        }
        $abonnement->setCount($abonnement->getCount()+1);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();
        //$this->sendReservation($reservation,$reservationRepository);
        
        $this->addFlash('success', 'La réservation a été ajoutée avec succès.');
       // $reservationRepository->sms($nomClient);

        return $this->redirectToRoute('list_reservation');
    }

    return $this->render('reservation/newr.html.twig', [
        'form' => $form->createView(),
        'reservations' => $reservations,
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


    public function sendReservation(Reservation $reservation,ReservationRepository $reservationRepository){
        //recupérer l id de utilisateur connecté
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
        $UserEmail = $user->getEmail();
        $idu= $user->getId();
    }

        $transport = Transport::fromDsn('smtp://khalilherma6@outlook.fr:KhAlIl332810@smtp.office365.com:587');
        $mailer = new Mailer($transport); 
        $email = (new Email());
        $email->from('khalilherma6@outlook.fr');
        $email->to($UserEmail);
        $email->subject('Abonnement réservé');

           //si on veut ajouter un pdf facture pour que le client peur la telecharger
           // $email->attachFromPath('C:/Users/marni/OneDrive/Bureau/New folder/PIDEV-Golden-Gym/Pidev/public/img/logo1.png');
           ///image
        $email->embed(fopen('../public/img/logo1.png', 'r'), 'nature');
        
           ///
        $reservations = $reservationRepository->findByUserId($idu);
        $email->html('merci d etre abonné');
        $mailer->send($email);
        $this->addFlash('success', 'la comfirmation du reservation est envoyer à ce émail :'. $UserEmail .' merci d\'avoir effectué vos achats sur GOLDENGYM!.');
        return true;
    }




    // #[Route('/apiAbonnementlist', name: 'apiAbonnement')]
    // public function APIlistAbonnement(AbonnementRepository $repo, NormalizerInterface $normalizer)
    // {
    //     $abonnement=$repo->findAll();
    //     $abonnementNormalises= $normalizer->normalize($abonnement,'json',['attributes' => ['nomAbonnement','id','prixAbonnement','dureeAbonnement']]);
    //     $json = json_encode($abonnementNormalises);

    //     return new Response($json);
    // }

    // #[Route('/apiReservationlist', name: 'apiReservation')]
    // public function APIlistReservation(ReservationRepository $repo, NormalizerInterface $normalizer)
    // {
    //     $reservation=$repo->findAll();
    //     $reservationNormalises= $normalizer->normalize($reservation,'json',['attributes' => ['DateDebut','DateFin','id']]);
    //     $json = json_encode($reservationNormalises);

    //     return new Response($json);
    // }

//     #[Route('/apiAddreservation', name: 'apiAddReservation')]
// public function addReservationJSON(Request $req, NormalizerInterface $normalizer)
// {
//     $em = $this->getDoctrine()->getManager();
//     $reservation = new Reservation();
//     $dateDebut = new \DateTime('@' . strtotime('now'));
//     $dateFin = new \DateTime('@' . strtotime('now +1 months'));
//     if ($dateDebut !== false) {
//         $reservation->setDateDebut($dateDebut);
//     } else {
//         // Handle the case where $dateDebutStr is not a valid date
//         // For example, you can set a default value or display an error message.
//     }
//     if($dateFin !== false){
//         $reservation->setDateFin($dateFin);
//     } else {
//         // Handle the case where $dateFinStr is not a valid date
//         // For example, you can set a default value or display an error message.
//     }
//     // Récupérer l'abonnement à partir de l'ID fourni dans la requête
//     // $abonnementId = $req->get('reservation_abonnement_id');
//     // $abonnement = $em->getRepository(Abonnement::class)->find($abonnementId);
//     // $reservation->setReservationAbonnement($abonnement);
//     $jsonContent = null;
//     if ($reservation->getDateDebut() !== null && $reservation->getDateFin() !== null ) {
//         $em->persist($reservation);
//         $em->flush();
    
//         $jsonContent = $normalizer->normalize($reservation, 'json', ['attributes' => ['DateDebut', 'DateFin']]);
//     } else {
//         $jsonContent = ['error' => 'Invalid dates for the reservation'];
//     }
//     return new Response(json_encode($jsonContent));
// }

    
    






}
