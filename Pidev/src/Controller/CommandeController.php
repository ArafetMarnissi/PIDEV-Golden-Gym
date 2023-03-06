<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\User;
use App\Form\CommandeType;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\UserRepository;
use App\service\ServiceCommande;
use App\service\servicePdf;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use TCPDF;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CommandeController extends AbstractController
{

    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    #[Route('/ajouterCommande', name: 'ajouter_commande')]
    public function  add(CommandeRepository $commandeRepository,ManagerRegistry $doctrine, Request  $request, UserRepository $userRepository,SessionInterface $session, ProduitRepository $produitRepository,LigneCommandeRepository $ligneCommandeRepository): Response
    {
        $total=$this->CalculPrixTotal($session,$produitRepository);
        


        $commande = new commande();
        //recupérer l id de utilisateur connecté
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
        $id = $user->getId();
        $nomClient= $user->getNom();

       }
       // ajouter l utilisateur a la commande
        $commande->setUser($userRepository->find($id));
        $commande->setPrixCommande($total);
        

        $form = $this->createForm(commandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            //tester si la paiement a la livraison on ajoute 7 dt
            if ($commande->getMethodePaiement() == 'à la livraison') {
                $commande->setPrixCommande($total + 7);
            }
            $em->persist($commande);
            $em->flush();
           
            
            //parcourir le panier, pour chaque element du panier est instancié un objet ligne commande  

            $panier = $session->get('panier', []);
            foreach ($panier as $id => $quantity) {
               
                
                $LingeCommande= new LigneCommande();
                $LingeCommande->setCommande($commande);
                $LingeCommande->setProduits($produitRepository->find($id));
                $LingeCommande->setPrixUnitaire($produitRepository->find($id)->getPrixProduit());
                $LingeCommande->setQuantiteProduit($quantity);
                $em->persist($LingeCommande);
                $em->flush();
                unset($panier[$id]);
                $session->set('panier', $panier);
               // $this->sendFacture($commande,$ligneCommandeRepository);
                //$commandeRepository->sms($nomClient,$commande->getTelephone());
            }
           

            return $this->redirectToRoute('AffichageCommandeClient');
        }
        return $this->renderForm(
            "commande/add_edit_Commande.html.twig",
            ["formCommande" => $form, "editMode" => $commande->getId() !== null,'PrixTotal'=>$total]
        );
    }

    #[Route('/affichageCommandeBack', name: 'Affichagecommande')]
    public function list(ManagerRegistry $doctrine,ServiceCommande $serviceCommande): Response
    {
        //$repository = $doctrine->getRepository(commande::class);
        $commandes = $serviceCommande->getPaginetCommande();
        return $this->render('commande/AffichageCommandeBack.html.twig',[
            'commandes' => $commandes,
        ]);
    }

    #[Route('/updatecommande/{id}', name: 'modifier_commande')]
    public function  update(ManagerRegistry $doctrine, $id,  Request  $request): Response
    {
        
        $commande = $doctrine
            ->getRepository(commande::class)
            ->find($id);
            $total=$commande->getPrixCommande();
            
        $form = $this->createForm(commandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('Affichagecommande');
        }
        return $this->renderForm(
            "commande/add_edit_Commande.html.twig",
            [
                "formCommande" => $form,
                "editMode" => $commande->getId() !== null,
                'PrixTotal'=>$total
            ]
        );
    }

    #[Route('/deletecommande/{id}', name: 'supprimer_commande')]
    public function DeleteCommande(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(commande::class);
        $commande = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($commande);
        $em->flush();
        return $this->redirectToRoute('Affichagecommande');
    }

    ///claculer le prix totale des produits dans le panier
    public function CalculPrixTotal(SessionInterface $session, ProduitRepository $produitRepository){
        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = 0;
        foreach ($panierWithData as $item) {
            $total += $item['product']->getPrixProduit() * $item['quantity'];
            
        }
        return $total;

    }
    //afficher la liste de commandes pour le client
    #[Route('/affichageCommandeClient', name: 'AffichageCommandeClient')]
    public function commandeC(CommandeRepository $commandeRepository,ServiceCommande $serviceCommande): Response
    {
        //recupérer l id de utilisateur connecté
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
        $UserId = $user->getId();
       }
       // rcupérer la liste de commande 
           

        
        //$commandes = $commandeRepository->findByUserId($UserId);
        $commandes = $serviceCommande->getPaginetCommandeClient($UserId);
        return $this->render('commande/MesCommandes.html.twig',[
            'commandes' => $commandes,
            
        ]);
    }

    ///modifer une commande coté client

    #[Route('/updatecommandeClient/{id}', name: 'modifier_commandeClient')]
    public function  updateCClient(ManagerRegistry $doctrine, $id,  Request  $request): Response
    {
        
        $commande = $doctrine
            ->getRepository(commande::class)
            ->find($id);
            $total=$commande->getPrixCommande();
            
        $form = $this->createForm(commandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('AffichageCommandeClient');
        }
        return $this->renderForm(
            "commande/add_edit_Commande.html.twig",
            [
                "formCommande" => $form,
                "editMode" => $commande->getId() !== null,
                'PrixTotal'=>$total
            ]
        );
    }
    ///envoyer un mail au client contenant une facture

    public function sendFacture(Commande $commande,LigneCommandeRepository $ligneCommandeRepository){
         //recupérer l id de utilisateur connecté
         $user = $this->getUser();
         if ($user instanceof \App\Entity\User) {
         $UserEmail = $user->getEmail();
        }
        ///preparer la facture
        $idCommande=$commande->getId();
        $ligneCommande = new LigneCommande();
        $ligneCommande=$ligneCommandeRepository->findByCommandeId($idCommande);
        $content = $this->renderView('facture/facture.html.twig', array(
            'commande' => $commande,
            'ligneCommande' => $ligneCommande,
        ));

        
            $transport = Transport::fromDsn('smtp://ggym45@outlook.com:Arafet26845815@smtp.office365.com:587');
            $mailer = new Mailer($transport); 
            $email = (new Email());
            $email->from('ggym45@outlook.com');
            $email->to($UserEmail);
            $email->subject('Command Confirmed');

            //si on veut ajouter un pdf facture pour que le client peur la telecharger
            // $email->attachFromPath('C:/Users/marni/OneDrive/Bureau/New folder/PIDEV-Golden-Gym/Pidev/public/img/logo1.png');
            ///image
           // $email->embed(fopen('../public/img/logo1.png', 'r'), 'nature');
            $pdf=$this->pdf($commande,$ligneCommandeRepository);
            $pdfContent = $pdf->Output('facture.pdf', 'S');
            $email->attach($pdfContent,"facture.pdf","Application/pdf");
            
            ///

            $email->html($content);
            $mailer->send($email);

   
           //$this->addFlash('success', 'la facture est envoyer à ce émail :'. $UserEmail .' merci d\'avoir effectué vos achats sur GOLDENGYM!.');

        return true;

    }
    /////creer une facture
    #[Route('/facture', name: 'facture')]
    public function createFacture(CommandeRepository $commandeRepository,LigneCommandeRepository $ligneCommandeRepository){

        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
        $UserEmail = $user->getEmail();
       }
        $commande = new Commande();
        $commande=$commandeRepository->find(65);
        $ligneCommande = new LigneCommande();
        $ligneCommande=$ligneCommandeRepository->findByCommandeId(65);
        return $this->render('facture/pdftest.html.twig',[
            'commande' => $commande,
            'ligneCommande' => $ligneCommande,
            'client'=>$user,
        ]);


    }

    ////creer un pdf
    
//controller
// #[Route('/pdf', name:"PDF_Article", methods:("GET"))]
     
public function pdf(Commande $commande,LigneCommandeRepository $ligneCommandeRepository)
{
   
            //recupérer l id de utilisateur connecté
            $user = $this->getUser();
           ///preparer la facture
        //    $commande=$commandeRepository->find(70);
           $idCommande= $commande->getId();
           $ligneCommande = new LigneCommande();
           $ligneCommande=$ligneCommandeRepository->findByCommandeId($idCommande);
 // Récupérer le contenu du template HTML
 $html = $this->renderView('facture/pdftest.html.twig', [
    'commande' => $commande,
    'ligneCommande' => $ligneCommande,
    'client'=>$user, 
]);
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);

return $mpdf;

   
   
}



//////////////////////////////*****************CODENAMEONE*******************//////////////////////////////
//////affichage des commande
#[Route('/apiAffichageCommandeBack', name: 'ApiAffichagecommande')]
    public function listCommande(CommandeRepository $commandeRepository,NormalizerInterface $normalizerInterface): Response
    {
        $commandes = $commandeRepository->findAll();
        foreach ($commandes as $commande) {
            $userId=$commande->getUser()->getId();
            $commande->setUserId($userId);
            
        }
        
        $commandesNormalises=$normalizerInterface->normalize($commandes,'json',['groups' => 'commandes']);
        $json= json_encode($commandesNormalises,JSON_UNESCAPED_UNICODE);
        return new Response($json, 200, ['Content-Type' => 'application/json; charset=utf-8']);
    }
///////Afficher les Commandes d'un Client
  //afficher la liste de commandes pour le client
  #[Route('/apiAffichageCommandeClient/{idUser}', name: 'ApiAffichageCommandeClient')]
  public function commandeClient($idUser,CommandeRepository $commandeRepository,NormalizerInterface $normalizerInterface): Response
  {
          $commandes = $commandeRepository->findByUserId($idUser);
          foreach ($commandes as $commande) {
            $commande->setUserId($idUser);
            
        }
        
        $commandesNormalises=$normalizerInterface->normalize($commandes,'json',['groups' => 'commandes']);
        $json= json_encode($commandesNormalises,JSON_UNESCAPED_UNICODE);
        return new Response($json, 200, ['Content-Type' => 'application/json; charset=utf-8']);
     
  }

///////ajouter une commande
#[Route('/apiAjouterCommande', name: 'Api_ajouter_commande')]
public function  ajouterCommande(Request  $request, UserRepository $userRepository, ProduitRepository $produitRepository,LigneCommandeRepository $ligneCommandeRepository,NormalizerInterface $normalizerInterface): Response
{
    $em = $this->getDoctrine()->getManager();
    $commande = new commande();
    $commande->setAdresseLivraison($request->get('adresse_livraison'));
    $commande->setMethodePaiement($request->get('methode_paiement'));
    //$commande->setPrixCommande($request->get('prix_commande'));
    $commande->setTelephone($request->get('telephone'));
    $UserId=$request->get('user_id');
    $user=$userRepository->find($UserId);
    $commande->setUser($user);
        // //parcourir l'url, pour chaque produit du url est instancié un objet ligne commande  
        $totalPrix=0;
        
        foreach ($request->query->all() as $parametre => $valeur) {
            if (strpos($parametre, 'produit_') === 0) {
                $produitId = substr($parametre, strlen('produit_'));
                
                $quantite = $request->query->get('produit_' . $produitId);
                //////crerer les ligne de commande
                $LingeCommande= new LigneCommande();
                $LingeCommande->setCommande($commande);
                $LingeCommande->setProduits($produitRepository->find($produitId));
                $LingeCommande->setPrixUnitaire($produitRepository->find($produitId)->getPrixProduit());
                $LingeCommande->setQuantiteProduit($quantite);
                $totalPrix += ($produitRepository->find($produitId)->getPrixProduit())*$quantite;
                $em->persist($LingeCommande);


            }
        }
        $commande->setPrixCommande($totalPrix);
        $em->persist($commande);
        $em->flush();

        //     $this->sendFacture($commande,$ligneCommandeRepository);

 
       
        $jsonContent=$normalizerInterface->normalize($commande,'json',['groups' => 'commandes']);
        return new Response("Commande added successfully ".json_encode($jsonContent));
    }
    
    /////supprimer une commande
    #[Route('/apiDeletecommande/{id}', name: 'ApiSupprimer_commande')]
    public function DeleteComApi(CommandeRepository $commandeRepository, $id,NormalizerInterface $normalizerInterface): Response
    {
        
        $commande = $commandeRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($commande);
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($commande,'json',['groups' => 'commandes']);
        return new Response("Commande deletd successfully ".json_encode($jsonContent));
    }
    /////modifer une commande
    #[Route('/apiUpdatecommandeClient/{id}', name: 'Apimodifier_commandeClient')]
    public function  updateCommande(CommandeRepository $commandeRepository, $id,  Request  $request): Response
    {
        $commande = $commandeRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        $commande->setAdresseLivraison($request->get('adresse_livraison'));
        $commande->setMethodePaiement($request->get('methode_paiement'));
        $commande->setTelephone($request->get('telephone'));
        $em->flush();
        return new Response("Commande updated successfully ");
         
        
       
    }
    




  
}
