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
use Normalizer;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use ReCaptcha\ReCaptcha;


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
            $email->embed(fopen('../public/img/logo1.png', 'r'), 'logo');

            $email->html('Voici votre code : <b>'  . $randomInt . '</b>:<br><img src="cid:logo"><br>Thanks,<br>Admin');
            $mailer->send($email);

   
            $this->addFlash('success', 'Un code a été envoye à l\'adresse suivante :'. $UserEmail .' Veuillez se connecter  afin de confirmer votre compte GoldenGym.');
            
            return $this->redirectToRoute('app_login');                


           
        }
    

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/AddUserJson/new', name: 'AddUser_Json')]
    public function addUserJson(Request $req, NormalizerInterface $normalizer)
    {
        $randomInt = random_int(100000, 999999);

        $em=$this->getDoctrine()->getManager();
        $user = new User();
        $user->setRoles(['ROLE_CLIENT']);
        $user->setNom($req->get('nom'));
        $user->setPrenom($req->get('prenom'));
        $user->setEmail($req->get('email'));
        $user->setPassword($req->get('password'));
        $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
        $user->setStatus(false);
        $user->setPrivateKey($randomInt);
        $em->persist($user);    
        $em->flush();
        $transport = Transport::fromDsn('smtp://khalilherma6@outlook.fr:KhAlIl332810@smtp.office365.com:587');
            $mailer = new Mailer($transport); 
            $email = (new Email());
            $email->from('khalilherma6@outlook.fr');
            $email->to($req->get('email'));
            $email->subject('Confirmation de votre compte GOLDENGYM');
            $email->embed(fopen('../public/img/logo1.png', 'r'), 'logo');

            $email->html('Voici votre code : <b>'  . $randomInt . '</b>:<br><img src="cid:logo"><br>Thanks,<br>Admin');
            $mailer->send($email);

        $jsonContent = $normalizer->normalize($user,'json',['groups'=>'users']);
        return new Response("User Added Succesfully" . json_encode($jsonContent));


    }
//****************Sign In APi ************************/
    #[Route('user/signin', name: 'user_signin')]
    public function signinAPI(Request $request,NormalizerInterface $normalizer)
    {
        $email = $request->query->get('email');
        $password = $request->query->get('password');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email'=> $email] );

        if($user)
        {
            if(password_verify($password,$user->getPassword()))
            {
                $userNormalises= $normalizer->normalize($user,'json',['attributes' => ['id','email','password','roles','nom','prenom','PrivateKey','Status']]);
                $json = json_encode($userNormalises);
                return new Response($json);

            }
            else{
                return new Response("password not found");

            }
        }
        else
        {
            return new Response("User Not Found");

        }
    }



    /*********************Edit user API************ */
    #[Route('user/editUser', name: 'edit_user_api')]
    public function editUserApi(Request $request)
    {
        $id = $request->get('id');
        $email = $request->query->get('email');
        $password = $request->query->get('password');
        $em=$this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
    
        try
        {
            $em= $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse ("success",200);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse ("exception",$ex->getMessage());

        }

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
