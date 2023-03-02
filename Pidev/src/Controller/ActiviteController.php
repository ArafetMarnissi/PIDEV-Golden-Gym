<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\ActiviteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
<<<<<<< HEAD
    public function  add(ManagerRegistry $doctrine, Request  $request,SluggerInterface $slugger) : Response
    { $activite = new Activite() ;
        $form = $this->createForm(ActiviteType::class, $activite);
        #$form->add('ajouter', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {    $imageFile = $form->get('Image')->getData();
=======
    public function  add(ManagerRegistry $doctrine, Request  $request, SluggerInterface $slugger): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        #$form->add('ajouter', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('Image')->getData();
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
<<<<<<< HEAD
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
=======
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1

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
<<<<<<< HEAD
            
=======
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
            $em = $doctrine->getManager();
            $em->persist($activite);
            $em->flush();


            return $this->redirectToRoute('AffichageActivite');
        }
<<<<<<< HEAD
        return $this->renderForm("activite/index.html.twig",
            ["f"=>$form]) ;
=======
        return $this->renderForm(
            "activite/index.html.twig",
            ["f" => $form]
        );
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
    }

    #[Route('/affichageActivite', name: 'AffichageActivite')]
    public function list(ManagerRegistry $doctrine): Response
    {
<<<<<<< HEAD
        $repository= $doctrine->getRepository(Activite::class);
        $Activites=$repository->findAll();
=======
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->findAll();
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
        return $this->render('activite/indexAffichage.html.twig', [
            'activites' => $Activites,
        ]);
    }

    #[Route('/affichageActiviteF', name: 'AffichageActiviteF')]
    public function listFront(ManagerRegistry $doctrine): Response
    {
<<<<<<< HEAD
        $repository= $doctrine->getRepository(Activite::class);
        $Activites=$repository->findAll();
=======
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->findAll();
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
        return $this->render('activite/AffichageListActiviteFront.html.twig', [
            'activites' => $Activites,
        ]);
    }


    #[Route('activiteFront/get/{id}', name: 'getidFront')]
<<<<<<< HEAD
    public function show_id(ManagerRegistry $doctrine,$id): Response
    {
        $repository= $doctrine->getRepository(Activite::class);
        $Activites=$repository->find($id);
=======
    public function show_id(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Activite::class);
        $Activites = $repository->find($id);
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
        return $this->render('activite/detailActiviteFront.html.twig', [
            'activites' => $Activites,
            'id' => $id,
        ]);
    }

    #[Route('/updateActivite/{id}', name: 'updateActivite')]
<<<<<<< HEAD
    public function  update(ManagerRegistry $doctrine,$id,  Request  $request,SluggerInterface $slugger) : Response
    { $activite = $doctrine
        ->getRepository(Activite::class)
        ->find($id);
        $form = $this->createForm(ActiviteType::class, $activite);
        #$form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {         $imageFile = $form->get('Image')->getData();
=======
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
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
<<<<<<< HEAD
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
=======
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1

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
<<<<<<< HEAD
            
=======
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('AffichageActivite');
        }
<<<<<<< HEAD
        return $this->renderForm("activite/indexUpdate.html.twig",
            ["f"=>$form]) ;


=======
        return $this->renderForm(
            "activite/indexUpdate.html.twig",
            ["f" => $form]
        );
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
    }

    #[Route('/deleteActivite/{id}', name: 'deleteActivite')]
    public function DeleteS(ManagerRegistry $doctrine, $id): Response
    {
<<<<<<< HEAD
        $repository= $doctrine->getRepository(Activite::class);
        $activite=$repository->find($id);
        $em= $doctrine->getManager();
=======
        $repository = $doctrine->getRepository(Activite::class);
        $activite = $repository->find($id);
        $em = $doctrine->getManager();
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
        $em->remove($activite);
        $em->flush();
        return $this->redirectToRoute('AffichageActivite');
    }
<<<<<<< HEAD

=======
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
}
