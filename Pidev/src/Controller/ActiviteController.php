<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Participation;
use App\Form\ActiviteType;
use App\Repository\ActiviteRepository;
use App\Repository\CoachRepository;
use App\Repository\ParticipationRepository;
use App\Services\QrcodeService;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Builder\Method as BuilderMethod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Builder\Method;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActiviteController extends AbstractController
{
    #[Route('/activite', name: 'app_activite')]
    public function index(): Response
    {
        return $this->render('activite/index.html.twig', [
            'controller_name' => 'ActiviteController',
        ]);
    }

    #[Route('/addActivite', name: 'addActivite')]
    public function  add(ManagerRegistry $doctrine, Request  $request, SluggerInterface $slugger,ActiviteRepository $re): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        #$form->add('ajouter', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('Image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('Image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $activite->setImage($newFilename);
            }
            
            $em = $doctrine->getManager();
            $em->persist($activite);
            $em->flush();
            $re->sms();

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
        $tri=false;
        $tri1=false;
        $tri2=false;
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->findAll();
        return $this->render('activite/indexAffichage.html.twig', [
            'activites' => $Activites,
            'tri' => $tri,
            'tri1' => $tri1,
            'tri2' => $tri2,
        ]);
    }

    #[Route('/affichageActiviteF', name: 'AffichageActiviteF')]
    public function listFront(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->Affichage();
        return $this->render('activite/AffichageListActiviteFront.html.twig', [
            'activites' => $Activites,
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

    #[Route('activiteFront/get/{id}', name: 'getidFront')]
    public function show_id(ManagerRegistry $doctrine, $id, QrcodeService $qrcodeService): Response
    {
        $qrCode = null;
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->find($id);
        $qrshow="le coach est ".$Activites->getCoach()->getNomCoach()." son age est ".$Activites->getCoach()->getAgeCoach();
        $qrCode = $qrcodeService->qrcode($qrshow);
        return $this->render('activite/detailActiviteFront.html.twig', [
            'activites' => $Activites,
            'id' => $id,
            'enable' => $this->en($doctrine->getRepository(Participation::class),$id,$this->getUser()),
            'qrcode' => $qrCode
        ]);
    }

    #[Route('/updateActivite/{id}', name: 'updateActivite')]
    public function  update(ManagerRegistry $doctrine, $id,  Request  $request, SluggerInterface $slugger): Response
    {
        $activite = $doctrine
            ->getRepository(Activite::class)
            ->find($id);
        $form = $this->createForm(ActiviteType::class, $activite);
        #$form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('Image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('Image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $activite->setImage($newFilename);
            }
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

    #[Route('/activite/planning', name: 'activite_planning')]
    public function index1(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->findAll();
        
        $list = [];
        foreach($Activites as $act)
        {
            $start = $act->getDateActivite()->format('Y-m-d').' '.$act->getTimeActivite()->format('H:i:s');
            $end = $act->getDateActivite()->format('Y-m-d').' '.$act->getEnd()->format('H:i:s');
            $list[] = [
                'id' => $act->getId(),
                'start' => $start,
                'end' => $end,
                'title' => $act->getNomAcitivite(),
                'description' => $act->getNbrePlace(),
                'backgroundColor' => $act->getBackgroundColor(),
                'borderColor' => $act->getBorderColor(),
                'textColor' => $act->getTextColor(),
            ];
        }
        $data = json_encode($list);
        return $this->render('activite/CalendrierFront.html.twig', compact('data')
        )  ;
    }

    #[Route('/activite/planningBack', name: 'activite_planningBack')]
    public function indexBack(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->findAll();
        
        $list = [];
        foreach($Activites as $act)
        {
            $start = $act->getDateActivite()->format('Y-m-d').' '.$act->getTimeActivite()->format('H:i:s');
            $end = $act->getDateActivite()->format('Y-m-d').' '.$act->getEnd()->format('H:i:s');
            $list[] = [
                'id' => $act->getId(),
                'start' => $start,
                'end' => $end,
                'title' => $act->getNomAcitivite(),
                'description' => $act->getNbrePlace(),
                'backgroundColor' => $act->getBackgroundColor(),
                'borderColor' => $act->getBorderColor(),
                'textColor' => $act->getTextColor(),
            ];
        }
        $data = json_encode($list);
        return $this->render('activite/CalendrierBack.html.twig', compact('data')
        )  ;
    }

    #[Route('/addActiviteMobile', name: 'AddActiviteMobile')]
    public function ajoutMobile(Request $req,CoachRepository $repo)
    {
        $id=2;
        $time1 = '13:00:00';
        $time2 = '14:00:00';
        $coach = $repo->find($id);
        $activite=new Activite();
        $nomActivite=$req->query->get("nomAcitivite");
        $desc=$req->query->get("descriptionActivite");
        $nbre=$req->query->get("nbrePlace");
        $em = $this->getDoctrine()->getManager();
        $activite->setNomAcitivite($nomActivite);
        $activite->setDescriptionActivite($desc);
       // $activite->setNbrePlace(10);
        $activite->setNbrePlace($nbre);
        $activite->setCoach($coach);
        $activite->setBackgroundColor('#ff0000');
        $activite->setBorderColor('#ff0000');
        $activite->setTextColor('#ffffff');
        $activite->setDateActivite(new \DateTime('now'));
        $activite->setTimeActivite(new \DateTime($time1));
        $activite->setEnd(new \DateTime($time2));
        $activite->setImage('rpm-mills-800x534-1-64032742659b2.jpg');
        $activite->setDureeActivite(new \DateTime('now'));
        $em->persist($activite);
        $em->flush();
        $ser=new Serializer([new ObjectNormalizer()]);
        $formatted=$ser->normalize($activite);
        return new JsonResponse($formatted);
    }

    #[Route('/affichageActiviteMobile', name: 'AffichageActiviteMobile')]
    public function listMobile(ManagerRegistry $doctrine, NormalizerInterface $normalizer)
    {
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->findAll();
        $activitesNormalises = $normalizer->normalize($Activites, 'json', ['groups' => "activites"]);
        //$serializer = new Serializer([new ObjectNormalizer()])
        $json = json_encode($activitesNormalises);
        return new Response($json);
    }

    #[Route('activiteMobile/get/{id}', name: 'getidMobile')]
    public function show_id_Mobile(ManagerRegistry $doctrine, $id, NormalizerInterface $normalizer)
    {
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->find($id);
        $activitesNormalises = $normalizer->normalize($Activites, 'json', ['groups' => "activites"]);
        $json = json_encode($activitesNormalises);
        return new Response($json);
    }

    #[Route('activite/modifierCalendrier/{id}', name: 'ModifCalendrier')]
    public function ModifierCalendrier(ManagerRegistry $doctrine, $id,?Activite $calendar, Request $req)
    {
       $donnees= json_decode($req->getContent());
       if(
        isset($donnees->nomAcitivite) && !empty($donnees->nomAcitivite) &&
        isset($donnees->DateActivite) && !empty($donnees->DateActivite) &&
        isset($donnees->end) && !empty($donnees->end) &&
        
        isset($donnees->background_color) && !empty($donnees->background_color) &&
        isset($donnees->border_color) && !empty($donnees->border_color) &&
        isset($donnees->text_color) && !empty($donnees->text_color) &&
        isset($donnees->nbrePlace) && !empty($donnees->nbrePlace)
       ){
        
        
            $code = 200;
            /*if(!$calendar){
                $calendar = new Activite;
                $code = 201;
            }*/
            $calendar->setNomAcitivite($donnees->nomAcitivite);
            $calendar->setNbrePlace($donnees->nbrePlace);
            $calendar->setBackgroundColor($donnees->background_color);
            $calendar->setBorderColor($donnees->border_color);
            $calendar->setTextColor($donnees->text_color);
            $date_act=substr($donnees->DateActivite,0,10);
            $heure_deb=substr($donnees->DateActivite,11,8);
            $heure_fin=substr($donnees->end,11,8);
            
            $calendar->setEnd(\DateTimeImmutable::createFromFormat('H:i:s', $heure_fin));
            $calendar->setTimeActivite(\DateTimeImmutable::createFromFormat('H:i:s', $heure_deb ));
            $calendar->setDateActivite(\DateTimeImmutable::createFromFormat('Y-m-d', $date_act));
            
            $em = $doctrine->getManager();
            $em->persist($calendar);
            $em->flush();
  
            return new Response($code);
       }else{
            return new Response('données manquantes', 404);
           
       }
    }

    #[Route('/affichageActiviteTriN', name: 'AffichageActiviteTriN')]
    public function listTriParNom(ManagerRegistry $doctrine): Response
    {
        $tri=true;
        $tri1=false;
        $tri2=false;
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->TriParNomActivite();
        return $this->render('activite/indexAffichage.html.twig', [
            'activites' => $Activites,
            'tri' => $tri,
            'tri1' => $tri1,
            'tri2' => $tri2,
        ]);
    }

    #[Route('/affichageActiviteTriD', name: 'AffichageActiviteTriD')]
    public function listTriParDate(ManagerRegistry $doctrine): Response
    {
        $tri1=true;
        $tri=false;
        $tri2=false;
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->TriParDateActivite();
        return $this->render('activite/indexAffichage.html.twig', [
            'activites' => $Activites,
            'tri1' => $tri1,
            'tri' => $tri,
            'tri2' => $tri2,
        ]);
    }

    #[Route('/affichageActiviteTriNb', name: 'AffichageActiviteTriNb')]
    public function listTriParNbre(ManagerRegistry $doctrine): Response
    {
        $tri2=true;
        $tri=false;
        $tri1=false;
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->TriParNbrePlaceActivite();
        return $this->render('activite/indexAffichage.html.twig', [
            'activites' => $Activites,
            'tri2' => $tri2,
            'tri1' => $tri1,
            'tri' => $tri,
        ]);
    }
   
}
