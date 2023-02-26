<?php

namespace App\Controller;
use App\Mailer\MailerService;
use App\Entity\User;
use App\Form\ConfirmFormType;
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
use phpDocumentor\Reflection\PseudoTypes\False_;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;
  

 
    
    #[Route('/home', name: 'home-page')]
    public function hello(): Response
    {
        return $this->render('home.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
      
    }


    #[Route('/inscription', name: 'security_registration')]
    public function registration(HttpFoundationRequest $request, PersistenceManagerRegistry $doctrine): Response
    {   

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $UserEmail = $form->getData()->getEmail();
            
            // Encode the new users password
            $randomInt = random_int(100000, 999999);
            $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
            $user->setStatus(false);
            $user->setPrivateKey($randomInt);
            // Set their role
            $user->setRoles(['ROLE_CLIENT']);

            // Save 
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();
            $transport = Transport::fromDsn('smtp://khalilherma6@outlook.fr:KhAlIl332810@smtp.office365.com:587');
            $mailer = new Mailer($transport); 
            $email = (new Email());
            $email->from('khalilherma6@outlook.fr');
            $email->to($UserEmail);
            $email->subject('Confirmation de votre compte GOLDENGYM');

            $email->html('Voici votre code : <b>'  . $randomInt . '</b> \n:<br><img src="public/img/logo1.png" width="200" height="200"><br>Thanks,<br>Admin');
            $mailer->send($email);

   
            $this->addFlash('success', 'Un code a été envoye à l\'adresse suivante :'. $UserEmail .' Veuillez se connecter  afin de confirmer votre compte GoldenGym.');
            
            return $this->redirectToRoute('app_login');                


           
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/confirmAccount', name: 'confirm_account')]
    public function confirmAccount(HttpFoundationRequest $request, PersistenceManagerRegistry $doctrine): Response
    {     
        
        $confirmForm = $this->createForm(ConfirmFormType::class);

        $confirmForm->handleRequest($request);

        if ($confirmForm->isSubmitted()) {
            $data = $confirmForm->getViewData()['code'];
            $enteredCode = intval($data);
           
            $user = $this->getUser();
            if ($user instanceof \App\Entity\User) {
                $code = $user->getPrivateKey();
                $id=$user->getId();
              
                
               if ($code == $enteredCode)
                {
                    $user->setStatus(true);
                    $em = $doctrine->getManager();
                    $em->persist($user);
                    $em->flush();
                    return $this->redirectToRoute('afficher_details_user',[
                        'id'=>$id
                    ]);
                }
                else 
                {
                    $this->addFlash('error', 'Le code que vous avez entré est incorrect.');

                }


            }
        }
        return $this->render('registration/confirmAccount.html.twig', [
            'confirmForm' => $confirmForm->createView()
        ]);
    }

}