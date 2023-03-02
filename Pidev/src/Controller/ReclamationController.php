<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReclamationRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\Persistence\ManagerRegistry;
use Mpdf\Mpdf;
use Dompdf\Options;
use Dompdf\Dompdf;


class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }
     /**
    * @Route("/listreclamation/", name="reclamation")
    */
   public function listReclamation(ReclamationRepository $repository)
   {
       $reclamations=$this->getDoctrine()->getRepository(Reclamation::class)->findAll();
       //$reclamations =$repository->findAll();
   
       return $this->render('reclamation/list.html.twig', array("reclamations" => $reclamations));
    }   
   
    /**
     * @Route("/addReclamation", name="addReclamation")
     */
    public function addReclamation(Request $request)
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reclamation);
            $em->flush();
            $this->addFlash(
                'info',
                'Reclamation ajoutée avec succés!'
            );
            return $this->redirectToRoute('addReclamation');
        }
        return $this->render("reclamation/index.html.twig",array('form'=>$form->createView()));
} 

    // /**
    //  * @Route("/deleteR/{id}", name="deleteReclamation")
    //  */
    // public function deleteReclamation(ManagerRegistry $doctrine, $id):Response
    // {
        
    //     $em = $this->getDoctrine()->getManager();
    //     $em->remove($reclamations);
    //     $em->flush();
    //     return $this->redirectToRoute('reclamations');
    // }
    #[Route('/deleteR/{id}',name:'deleteReclamation')]
public function delete (ManagerRegistry $doctrine, $id):Response
{   $repository=$doctrine->getRepository(Reclamation::class);
    $reclamation=$repository->find($id);
    $em= $doctrine->getManager();
    $em->remove($reclamation);
    $em->flush();
    $this->addFlash(
        'info',
        'Reclamation supprimée avec succés!'
    );
    return $this->redirectToRoute('reclamation');
}
/**
     * @Route("/stats", name="stats")
     */
    public function statistiques(ReclamationRepository $reclamation){
        // On va chercher toutes les types
        $menus = $reclamation->findAll();

//Data Category
        $Commande = $reclamation->createQueryBuilder('a')
            ->select('count(a.id)')
            ->Where('a.type_reclamation= :type_reclamation')
            ->setParameter('type_reclamation',"Commande")
            ->getQuery()
            ->getSingleScalarResult();
$Magasin= $reclamation->createQueryBuilder('a')
            ->select('count(a.id)')
            ->Where('a.type_reclamation= :type_reclamation')
            ->setParameter('type_reclamation',"Magasin")
            ->getQuery()
            ->getSingleScalarResult();
       /*  $Activité= $reclamation->createQueryBuilder('a')
            ->select('count(a.id)')
            ->Where('a.type_reclamation= :type_reclamation')
            ->setParameter('type_reclamation',"Activité")
            ->getQuery()
            ->getSingleScalarResult();
            $Abonnement= $reclamation->createQueryBuilder('a')
            ->select('count(a.id)')
            ->Where('a.type_reclamation= :type_reclamation')
            ->setParameter('type_reclamation',"Abonnement")
            ->getQuery()
            ->getSingleScalarResult(); */
     return $this->render('Stats/stats.html.twig', [
           /*  'nact' => $Activité,
            'nabon' => $Abonnement, */
            'nmag' => $Magasin,
            'ncom' => $Commande,


        ]);
    }
    /**
     * @Route("/listrec", name="listrec", methods={"GET"})
     */
    public function list(ReclamationRepository $FootRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfoptions = new Options();
        $pdfoptions->set('defaultFont', 'Arial');
        $pdfoptions->set('tempDir', '.\www\DaryGym\public\uploads\images');
// Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfoptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reclamation/pdf.html.twig', [
            'reclamations' => $FootRepository->findAll(),
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
"Attachment" => false
        ]);
    }
}
