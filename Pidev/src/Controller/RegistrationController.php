<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    #[Route('/home', name: 'home-page')]
    public function hello(): Response
    {
        return $this->render('home.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
<<<<<<< HEAD
    } 
=======
    }
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/inscription', name: 'security_registration')]
<<<<<<< HEAD
    public function registration(HttpFoundationRequest $request,PersistenceManagerRegistry $doctrine) : Response
    {   
=======
    public function registration(HttpFoundationRequest $request, PersistenceManagerRegistry $doctrine): Response
    {
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));

            // Set their role
            $user->setRoles(['ROLE_CLIENT']);

            // Save
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
