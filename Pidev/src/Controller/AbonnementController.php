<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AbonnementType;
use App\Entity\Abonnement;
use App\Controller\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ReservationRepository;
use App\Repository\AbonnementRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;



class AbonnementController extends AbstractController
{
    #[Route('/abonnement', name: 'abonnement_index' )]
    public function index(): Response
    {
        return $this->render('abonnement/index.html.twig', [
            'controller_name' => 'AbonnementController',
        ]);
    }

    #[Route('/new', name: 'abonnement_new')]
    public function new(Request $request): Response
{
    $abonnement = new Abonnement();
    $form = $this->createForm(AbonnementType::class, $abonnement);
    $form->handleRequest($request);
    

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($abonnement);
        $entityManager->flush();

        return $this->redirectToRoute('list_Abonnement');
    }

    return $this->render('abonnement/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/listA', name: 'list_Abonnement')]
public function list(ManagerRegistry $doctrine): Response 
{
    $repository= $doctrine->getRepository(Abonnement::class);
    $abonnements=$repository->FindAll(); 

    
    return $this->render('abonnement/index.html.twig', [
        'abonnements' => $abonnements,

    ]);

}

#[Route('/updateAbonnement/{id}', name: 'modifier_un_abonnement')]
public function update(ManagerRegistry $doctrine,Request $request, $id): Response
{
    $repository= $doctrine->getRepository(Abonnement::class);
    $abonnements=$repository->find($id);
    $form= $this->createForm(AbonnementType::class, $abonnements );
    $form->handleRequest($request);
    if($form->isSubmitted()){
        $em= $doctrine->getManager();
        $em->persist($abonnements);
        $em->flush();
        return $this->redirectToRoute('list_Abonnement'); 
    }
    return $this->render('abonnement/new.html.twig', [ 'form' => $form->createView()
]); 
}

#[Route('/delete/{id}',name:'delete_un_abonnement')]
public function delete (ManagerRegistry $doctrine, $id):Response
{   $repository=$doctrine->getRepository(Abonnement::class);
    $abonnement=$repository->find($id);
    $em= $doctrine->getManager();
    $em->remove($abonnement);
    $em->flush();
    return $this->redirectToRoute('list_Abonnement');
}

    //Trie par prix ordre desc

    
    #[Route('/order_By_Prix_desc', name:'order_By_Prix_desc', methods:["GET"])]
    public function order_By_Prix_desc(AbonnementRepository $abRepository): Response
    {
        return $this->render('abonnement/trie.html.twig', [
            'abo' => $abRepository->order_By_PRIX_desc(),
        ]);
    }

    #[Route('/order_By_Prix_asc', name:'order_By_Prix_asc', methods:["GET"])]
    public function order_By_Prix_asc(AbonnementRepository $abRepositoryasc): Response
    {
        return $this->render('abonnement/trieasc.html.twig', [
            'aboasc' => $abRepositoryasc->order_By_PRIX_ASC(),
        ]);
    }  

    #[Route('/AboPlusReseved', name:'MostReserved', methods:["GET"])]
    public function mostReservedAbonnement(AbonnementRepository $aboRepository)
    {
        $abos = $aboRepository->findMostSoldAbonnement();
        
        return $this->render('abonnement/most_reserved.html.twig', [
            'abos' => $abos,
        ]);
    }    

    #[Route('/stats',name:'stats')]
    public function statistique(AbonnementRepository $staRepository , ChartBuilderInterface $chartBuilder): Response
    {
        $sta=$staRepository->findAll();
        $nb = $staRepository->count([]);
       // var_dump($sta);
        $nomab = [];
        $plusreserve = [];
        // $nomab[]=['a','b','c','d','f','h'];
        //     $plusreserve []=[1,2,3,4,5,6];
        foreach ($sta as $stats) {
            $nomab[]=$stats->getNomDureeAbonnement();
        //     $nomab[]=['a','b','c','d','f','h'];
            $plusreserve []=$stats->getCount();
        }
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $nomab,
            'datasets' => [
                [
                    'label' => 'Statisques des abonnements réservés',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $plusreserve,
                ],
            ],
        ]);

    $chart->setOptions([/* ... */]);

        return $this->render('abonnement/stats.html.twig', [
            'chart' => $chart,
        ]);
    }

    #[Route('/apiAbonnementlist', name: 'apiAbonnement')]
    public function APIlistAbonnement(AbonnementRepository $repo, NormalizerInterface $normalizer)
    {
        $abonnement=$repo->findAll();
        $abonnementNormalises= $normalizer->normalize($abonnement,'json',['attributes' => ['id','nomAbonnement','prixAbonnement','dureeAbonnement']]);
        $json = json_encode($abonnementNormalises);

        return new Response($json);
    }

    #[Route('/apiAddAbonnement', name: 'apiAddAbonnement')]  
    public function addAbonnementJSON(Request $req, NormalizerInterface $normalizer)  
    {
        $em = $this->getDoctrine()->getManager();
        $abonnement = new Abonnement();
        $abonnement->setNomAbonnement($req->get('nom_abonnement'));
        $abonnement->setPrixAbonnement($req->get('prix_abonnement'));
        $abonnement->setDureeAbonnement($req->get('duree_abonnement'));
        $em->persist($abonnement);
        $em->flush();
        $jsonContent = null;
        $jsonContent = $normalizer->normalize($abonnement, 'json', ['attributes' => ['nomAbonnement', 'prixAbonnement','dureeAbonnement']]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/apiUpdateAbonnement/{id}', name: 'apiUpdateAbonnement')]  
    public function UpdateAbonnementJSON(Request $req,$id, NormalizerInterface $normalizer)  
    {
        $em = $this->getDoctrine()->getManager();
        $abonnement=$em->getRepository(Abonnement::class)->find($id);
        $abonnement->setNomAbonnement($req->get('nom_abonnement'));
        $abonnement->setPrixAbonnement($req->get('prix_abonnement'));
        $abonnement->setDureeAbonnement($req->get('duree_abonnement'));        
        $em->flush();
        $jsonContent = null;
        $jsonContent = $normalizer->normalize($abonnement, 'json', ['attributes' => ['nomAbonnement', 'prixAbonnement','dureeAbonnement']]);
        return new Response(json_encode($jsonContent));
    }    

    #[Route('/apiRemoveAbonnement/{id}', name: 'apiRemoveAbonnement')]  
    public function RemoveAbonnementJSON(Request $req,$id, NormalizerInterface $normalizer)  
    {
        $em = $this->getDoctrine()->getManager();
        $abonnement=$em->getRepository(Abonnement::class)->find($id);
        $em->remove($abonnement);
        $em->flush();
        $jsonContent = null;
        $jsonContent = $normalizer->normalize($abonnement, 'json', ['attributes' => ['nomAbonnement', 'prixAbonnement','dureeAbonnement']]);
        return new Response(json_encode($jsonContent));
    }       



} 
