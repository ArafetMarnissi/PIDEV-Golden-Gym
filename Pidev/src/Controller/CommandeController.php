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
use Symfony\Component\Mime;
use Symfony\Component\Mime\MimeTypes;

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
    public function  add(ManagerRegistry $doctrine, Request  $request, UserRepository $userRepository,SessionInterface $session, ProduitRepository $produitRepository,LigneCommandeRepository $ligneCommandeRepository): Response
    {
        $total=$this->CalculPrixTotal($session,$produitRepository);
        


        $commande = new commande();
        //recupérer l id de utilisateur connecté
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
        $id = $user->getId();
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
                $this->sendFacture($commande,$ligneCommandeRepository);

            }
           

            return $this->redirectToRoute('AffichageCommandeClient');
        }
        return $this->renderForm(
            "commande/add_edit_Commande.html.twig",
            ["formCommande" => $form, "editMode" => $commande->getId() !== null,'PrixTotal'=>$total]
        );
    }

    #[Route('/affichageCommandeBack', name: 'Affichagecommande')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(commande::class);
        $commandes = $repository->findAll();
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
    public function DeleteS(ManagerRegistry $doctrine, $id): Response
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
    public function commandeC(CommandeRepository $commandeRepository): Response
    {
        //recupérer l id de utilisateur connecté
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
        $UserId = $user->getId();
       }
       // rcupérer la liste de commande 
           

        
        $commandes = $commandeRepository->findByUserId($UserId);
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

        
            $transport = Transport::fromDsn('smtp://khalilherma6@outlook.fr:KhAlIl332810@smtp.office365.com:587');
            $mailer = new Mailer($transport); 
            $email = (new Email());
            $email->from('khalilherma6@outlook.fr');
            $email->to($UserEmail);
            $email->subject('[GoldenGym] Confirmation de commande');

            //si on veut ajouter un pdf facture pour que le client peur la telecharger
            // $email->attachFromPath('C:/Users/marni/OneDrive/Bureau/New folder/PIDEV-Golden-Gym/Pidev/public/img/logo1.png');
            ///image
            $email->embed(fopen('C:/Users/marni/OneDrive/Bureau/New folder/PIDEV-Golden-Gym/Pidev/public/img/logo1.png', 'r'), 'nature');
            
            ///

            $email->html('<img src="cid:nature" width="200" height="100">'. $content);
            $mailer->send($email);

   
           $this->addFlash('success', 'la facture est envoyer à ce émail :'. $UserEmail .' merci d\'avoir effectué vos achats sur GOLDENGYM!.');

        return true;

    }
    /////creer une facture
    #[Route('/facture', name: 'facture')]
    public function createFacture(CommandeRepository $commandeRepository,LigneCommandeRepository $ligneCommandeRepository){

        $commande = new Commande();
        $commande=$commandeRepository->find(32);
        $ligneCommande = new LigneCommande();
        $ligneCommande=$ligneCommandeRepository->findByCommandeId(32);
        return $this->render('test/index.html.twig',[
            'commande' => $commande,
            'ligneCommande' => $ligneCommande
        ]);


    }

  
}
