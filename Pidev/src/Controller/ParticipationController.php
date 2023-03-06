<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Participation;
use App\Entity\User;
use App\Form\ActiviteType;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use App\Services\QrcodeService;
use Doctrine\Persistence\ManagerRegistry;
use Egulias\EmailValidator\Parser\PartParser;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationController extends AbstractController
{
    #[Route('/participation', name: 'app_participation')]
    public function index(): Response
    {
        return $this->render('participation/index.html.twig', [
            'controller_name' => 'ParticipationController',
        ]);
    }
    public function en(ParticipationRepository $repository2,$id,$user) : Bool
    {
        $enable=$repository2->FindPartById($id,$user);
        if ($enable == NULL){
        return false;
        }else{ 
        return true;
        }
    }

    public function dateValid($part) : Bool
    {
        $dateA = $part->getActivite()->getDateActivite();
        $date = new \DateTime('@' . strtotime('now'));
        if ($dateA >= $date){
        return true;
        }else{ 
        return false;
        }
    }

    #[Route('/addParticipation/get/{id}', name: 'addParticipation')]
    public function  add(ManagerRegistry $doctrine, Request  $request, $id): Response
    {
        $part = new Participation();
        $form = $this->createForm(ParticipationType::class, $part);
        $repository2 = $doctrine->getRepository(Participation::class);
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->find($id);
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
        $idu = $user->getId();
        }   
        $enable = $this->en($repository2,$id,$user);
        /*$part->setDateParticipation(new \DateTime('now'));
        $part->setActivite($Activites);*/
        if ( $enable == false){
        $Activites->setNbrePlace($Activites->getNbrePlace()-1);
        $part->setDateParticipation(new \DateTime('now'));
        $part->setActivite($Activites);
        $part->setUser($user);
        $form->handleRequest($request);
        $em = $doctrine->getManager();
        $em->persist($part);
        $em->flush();
        return $this->redirectToRoute('AffichageActiviteF');
        } else {
        $Activites->setNbrePlace($Activites->getNbrePlace());
        return $this->redirectToRoute('AffichageActiviteF');}
       /* $form->handleRequest($request);
        $em = $doctrine->getManager();
        $em->persist($part);
        $em->flush();
            return $this->redirectToRoute('AffichageActiviteF');*/
        return $this->renderForm(
            "activite/AffichageListActiviteFront.html.twig",
            ["f" => $form, "id" => $id, "en" => $enable]
        );
    }

    #[Route('/affichageParticipation', name: 'AffichageParticipation')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Participation::class);
        $part = $repository->findAll();
        return $this->render('participation/index.html.twig', [
            'part' => $part,
        ]);
    }

    #[Route('/affichageParticipationUser', name: 'AffichageParticipationUser')]
    public function listUser(ManagerRegistry $doctrine,ParticipationRepository $repo, QrcodeService $qrcodeService): Response
    {
        $qrCode = null;
        $user = $this->getUser(); 
        //$repository = $doctrine->getRepository(Participation::class);
        $part = $repo->FindPartsById($user);
        for($i=0 ; $i < count($part) ; $i++)
        {
        $date = $part[$i]->getActivite()->getDateActivite()->format('d F Y');
        $time = $part[$i]->getActivite()->getTimeActivite()->format('H:i');
        $query = $part[$i]->getActivite()->getNomAcitivite().' aura lieu le '.$date.' à '.$time.' avec le coach '.$part[$i]->getActivite()->getCoach()->getNomCoach();
        $qrCode = $qrcodeService->qrcode($query);
        $qrCode_array[] = $qrCode;
        }
        return $this->render('participation/participationUser.html.twig', [
            'part' => $part,
           'qrCode' => $qrCode_array,
        ]);
    }

    #[Route('/deleteParticipation/{id}', name: 'deleteParticipation')]
    public function DeleteS(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Participation::class);
        $repository1 = $doctrine->getRepository(Activite::class);
        $part = $repository->find($id);
        $Activites = $repository1->find($part->getActivite()->getId());
        $em = $doctrine->getManager();
        $em->remove($part);
        $Activites->setNbrePlace($Activites->getNbrePlace()+1);
        $em->flush();
        return $this->redirectToRoute('AffichageParticipation');
    }

    #[Route('/deleteParticipationU/{id}', name: 'deleteParticipationU')]
    public function DeleteSU(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Participation::class);
        $repository1 = $doctrine->getRepository(Activite::class);
        $part = $repository->find($id);
        $Activites = $repository1->find($part->getActivite()->getId());
        $em = $doctrine->getManager();
        $em->remove($part);
        $Activites->setNbrePlace($Activites->getNbrePlace()+1);
        $em->flush();
        return $this->redirectToRoute('AffichageParticipationUser');
    }
}
