<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReponseRepository;
use App\Repository\ReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Entity\Reclamation;
use Knp\Component\Pager\PaginatorInterface;

class ReponseController extends AbstractController
{
    #[Route('/reponse', name: 'app_reponse')]
    public function index(): Response
    {
        return $this->render('reponse/index.html.twig', [
            'controller_name' => 'ReponseController',
        ]);
    }
     /**
     * @Route("/addReponse/{id}", name="repondre")
     */
    public function addReponse(Request $request,$id,ReclamationRepository $reclamationRepository)
    
    {  
        $date = new \DateTime('@'.strtotime('now'));

        
        $reponse = new Reponse();

        $reponse->setIdreclamation($reclamationRepository->find($id));
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setDateReponse($date);
            $em = $this->getDoctrine()->getManager();
            $em->persist($reponse);
            $em->flush();
        }
        return $this->render("reponse/index.html.twig",array('form'=>$form->createView()));
    }
    /**
    * @Route("/listreponse", name="listee")
    */
   public function listReponse(ReponseRepository $repository,PaginatorInterface  $paginator,Request $request)
   {
       $reponses=$this->getDoctrine()->getRepository(Reponse::class)->findAll();
       $rec=$paginator->paginate(
        $reponses,
        $request->query->getInt('page',1),
        1
    ) ;
       return $this->render('reponse/list.html.twig', array("reponses" => $rec));
   }
   #[Route('/deleteRep/{id}',name:'deleteReponse')]
   public function delete (ManagerRegistry $doctrine, $id):Response
   {   $repository=$doctrine->getRepository(Reponse::class);
       $reponse=$repository->find($id);
       $em= $doctrine->getManager();
       $em->remove($reponse);
       $em->flush();
       $this->addFlash(
           'info',
           'Reponse supprimée avec succés!'
       );
       return $this->redirectToRoute('listee');
   }

}
