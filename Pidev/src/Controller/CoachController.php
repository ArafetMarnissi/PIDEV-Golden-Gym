<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Form\CoachType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class CoachController extends AbstractController
{
    #[Route('/coach', name: 'app_coach')]
    public function index(): Response
    {
        return $this->render('coach/index.html.twig', [
            'controller_name' => 'CoachController',
        ]);
    }

    #[Route('/addCoach', name: 'addCoach')]
    public function  add(ManagerRegistry $doctrine, Request  $request,SluggerInterface $slugger) : Response
    { $coach = new Coach() ;
        $form = $this->createForm(CoachType::class, $coach);
        #$form->add('ajouter', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {    $imageFile = $form->get('Image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

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
                $coach->setImage($newFilename);
            }
            
            $em = $doctrine->getManager();
            $em->persist($coach);
            $em->flush();


            return $this->redirectToRoute('AffichageCoach');
        }
        return $this->renderForm("coach/index.html.twig",
            ["f"=>$form]) ;
    }

    #[Route('/affichageCoach', name: 'AffichageCoach')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Coach::class);
        $coach=$repository->findAll();
        return $this->render('coach/indexAffichage.html.twig', [
            'coach' => $coach,
        ]);
    }

    #[Route('/affichageCoachH', name: 'AffichageCoachH')]
    public function listHome(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Coach::class);
        $coach=$repository->findAll();
        return $this->render('home.html.twig', [
            'coach' => $coach,
        ]);
    }

    /*#[Route('/affichageCoachF', name: 'AffichageCoachF')]
    public function listFront(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Coach::class);
        $coach=$repository->findAll();
        return $this->render('coach/AffichageListCoachFront.html.twig', [
            'coach' => $coach,
        ]);
    }*/


    /*#[Route('activiteFront/get/{id}', name: 'getidFront')]
    public function show_id(ManagerRegistry $doctrine,$id): Response
    {
        $repository= $doctrine->getRepository(Activite::class);
        $Activites=$repository->find($id);
        return $this->render('activite/detailActiviteFront.html.twig', [
            'activites' => $Activites,
            'id' => $id,
        ]);
    }*/

    #[Route('/updateCoach/{id}', name: 'updateCoach')]
    public function  update(ManagerRegistry $doctrine,$id,  Request  $request,SluggerInterface $slugger) : Response
    { $coach = $doctrine
        ->getRepository(Coach::class)
        ->find($id);
        $form = $this->createForm(CoachType::class, $coach);
        #$form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {         $imageFile = $form->get('Image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

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
                $coach->setImage($newFilename);
            }
            
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('AffichageCoach');
        }
        return $this->renderForm("coach/indexUpdate.html.twig",
            ["f"=>$form]) ;


    }

    #[Route('/deleteCoach/{id}', name: 'deleteCoach')]
    public function DeleteS(ManagerRegistry $doctrine, $id): Response
    {
        $repository= $doctrine->getRepository(Coach::class);
        $coach=$repository->find($id);
        $em= $doctrine->getManager();
        $em->remove($coach);
        $em->flush();
        return $this->redirectToRoute('AffichageCoach');
    }
}
