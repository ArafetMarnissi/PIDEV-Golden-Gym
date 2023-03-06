<?php

namespace App\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use ApiPlatform\Core\Annotation\ApiResource;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\CoachRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\DocBlock\Serializer as DocBlockSerializer;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/listuser', name: 'app_user')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(User::class);
        $users=$repository->findAll();

        
           
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }
/*----------------Affichage User JSON -------------------*/

    #[Route('/apiuserlist', name: 'apiUser')]
    public function APIlistuser(UserRepository $repo, NormalizerInterface $normalizer)
    {
        $user=$repo->findAll();
        $userNormalises= $normalizer->normalize($user,'json',['attributes' => ['id','email','password','roles','nom','prenom','PrivateKey','Status']]);
        $json = json_encode($userNormalises);

        return new Response($json);
    }


    #[Route('/UserProfile/{id}',name:'afficher_details_user')]
    public function updateProfil(ManagerRegistry $doctrine , HttpFoundationRequest $req ,$id,SessionInterface $session)
    {
/* 

    $user=$this->getUser();
    
    dd("");
    */


    $repository= $doctrine->getRepository(User::class);
    $user=$repository->find($id);
     $form = $this-> createForm(UserType::class,$user);
     $form->handleRequest($req);
     if($form->isSubmitted() && $form->isValid() )
     {
        $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));

    
        $user->setRoles(['ROLE_CLIENT']);
        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('afficher_details_user',['id'=>$id]);
     
     }

    return $this->renderForm("front/consulterProfil.html.twig",
    ["form"=>$form]);
    }

    #[Route('/deleteUser/{id}', name: 'delete_user')]
    public function DeleteS(ManagerRegistry $doctrine, $id): Response
    {
        $repository= $doctrine->getRepository(User::class);
        $user=$repository->find($id);
        $em= $doctrine->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_user');
    }
/***************Delete User API************* */
    #[Route('/deleteUserAPI/{id}', name: 'delete_user_api')]
    public function DeleteUser(ManagerRegistry $doctrine, $id,NormalizerInterface $normalizer): Response
    {
        $repository= $doctrine->getRepository(User::class);
        $user=$repository->find($id);
        $em= $doctrine->getManager();
        $em->remove($user);
        $em->flush();
        $jsonContent= $normalizer->normalize($user,'json',['groups'=>'users']);
        return new Response("Student deleted successfully" . json_encode($jsonContent));
    }

    #[Route('/updateUser/{id}', name: 'update_user')]
    public function updateUser(ManagerRegistry $doctrine , HttpFoundationRequest $req ,$id )
    {
    $repository= $doctrine->getRepository(User::class);
    $user=$repository->find($id);
     $form = $this-> createForm(UserType::class,$user);
     $form->handleRequest($req);
     if($form->isSubmitted() && $form->isValid())
     {
        $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
        $em = $doctrine->getManager();
        $em->persist($user);

        $em->flush();
        return $this->redirectToRoute('app_user');
     
     }

    return $this->renderForm("user/modifierUser.html.twig",['form'=>$form,'users'=>$user]);
    } 
    

  

    


}
