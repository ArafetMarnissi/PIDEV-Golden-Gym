<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Form\ProduitType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(): Response
    {
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

    #[Route('/listp', name: 'list_produit')]
    public function listp(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Produit::class);
        $produits = $repository->findAll();
        $nb = $repository->count([]);
        for ($i = 0; $i<$nb; $i++)
        {
            if($produits[$i]->isproduitExpired())
            {
                $supp=$repository->find($produits[$i]->getId());
                $em = $doctrine->getManager();
                $em->remove($supp);
                $em->flush();
            }

        }
        for($i=0;$i<$nb;$i++){
            if($produits[$i]->getQuantiteProduit()<6 && $produits[$i]->getQuantiteProduit()!=0)
            {
                //$repository->sms($produits[$i]->getNom());
                $transport = Transport::fromDsn('smtp://khalilherma6@outlook.fr:KhAlIl332810@smtp.office365.com:587');
                $mailer = new Mailer($transport); 
                $email = (new Email());
                $email->from('khalilherma6@outlook.fr');
                $email->to('dammakkarim@gmail.com');
                $email->subject('expiration produit');
                $email->embed(fopen('../public/img/logo1.png','r','logo'));

                $email->html('le produit à expire :" <b>'  . $produits[$i]->getNom() . '"</b> :<br><img src="cid:logo" ><br>Thanks,<br>Admin');
                $mailer->send($email);
            }
            else if ($produits[$i]->getQuantiteProduit()==0 )
            {
                //$repository->sms1($produits[$i]->getNom());
                $transport = Transport::fromDsn('smtp://khalilherma6@outlook.fr:KhAlIl332810@smtp.office365.com:587');
                $mailer = new Mailer($transport); 
                $email = (new Email());
                $email->from('khalilherma6@outlook.fr');
                $email->to('dammakkarim@gmail.com');
                $email->subject('expiration produit');
                $email->embed(fopen('../public/img/logo1.png','r','logo'));

                $email->html('le produit à expire :" <b>'  . $produits[$i]->getNom() . '"</b> :<br><img src="cid:logo" ><br>Thanks,<br>Admin');
                $mailer->send($email);
            }
        }
        $prod = $repository->findAll();
        return $this->render('produit/listp.html.twig', [
            'produit' => $prod,
        ]);
    }

    #[Route('/addp', name: 'addp')]
    public function addp(HttpFoundationRequest $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        //$repository= $doctrine->getRepository(Produit::class);
        //$produits=$repository->findAll();
        $produit = new Produit;
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('imageProduit')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('produit_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $produit->setImageProduit($newFilename);
            }
            $em = $doctrine->getManager();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('list_produit');
        }
        return $this->renderForm('produit/new.html.twig', ['formp' => $form, "editmode" => $produit->getid() !== null]);
    }

    #[Route('/deletep/{id}', name: 'deletep')]
    public function deletep(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Produit::class);
        $produit = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('list_produit');
    }

    #[Route('/editp/{id}', name: 'editp')]
    public function editp(HttpFoundationRequest $request, ManagerRegistry $doctrine, $id, SluggerInterface $slugger): Response
    {
        $repository = $doctrine->getRepository(Produit::class);
        $produits = $repository->find($id);
        $form = $this->createForm(ProduitType::class, $produits);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('imageProduit')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('produit_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $produits->setImageProduit($newFilename);
            }
            $em = $doctrine->getManager();
            $produits->setnom($form->get('nom')->getData());
            $em->flush();
            return $this->redirectToRoute('list_produit');
        }
        return $this->renderForm('produit/new.html.twig', ['formp' => $form, "editmode" => $produits->getid() !== null]);
    }

    #[Route('/getf/{id}', name: 'gtidf')]
    public function show_idf(ManagerRegistry $doctrine, $id,QrcodeService $qrcodeService): Response
    {
        $qrCode = null;
        $repository = $doctrine->getRepository(produit::class);
        $produits = $repository->find($id);
        $qrCode = $qrcodeService->qrcode($produits->getNom());
        return $this->render('produit/detailf.html.twig', [
            'produits' => $produits,
            'id' => $id,
            'qrcode'=>$qrCode,
        ]);
    }

    #[Route('/listpf', name: 'list_produit_front')]
    public function listpf(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Produit::class);
        $produits = $repository->findAll();
        $nb = $repository->count([]);
        for ($i = 0; $i<$nb; $i++)
        {
            if($produits[$i]->isproduitExpired())
            {
                $supp=$repository->find($produits[$i]->getId());
                $em = $doctrine->getManager();
                $em->remove($supp);
                $em->flush();
            }

        }
        $prod = $repository->findAll();
        return $this->render('produit/listpf.html.twig', [
            'produit' => $prod,
        ]);
    }

    #[Route('/getf/{id}', name: 'gtidf')]
    public function show_idf(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(produit::class);
        $produits = $repository->find($id);
        return $this->render('produit/detailf.html.twig', [
            'produits' => $produits,
            'id' => $id,
        ]);
    }

    #[Route('/listpag/{page?1}/{nbr?3}', name: 'list_produitpag')]
    public function listpag(ManagerRegistry $doctrine,$page,$nbr): Response
    {
        $repository = $doctrine->getRepository(Produit::class);
        $produits = $repository->findAll();
        $nb = $repository->count([]);
        for ($i = 0; $i<$nb; $i++)
        {
            if($produits[$i]->isproduitExpired())
            {
                $supp=$repository->find($produits[$i]->getId());
                $em = $doctrine->getManager();
                $em->remove($supp);
                $em->flush();
            }

        }
        $nbproduit=$repository->count([]);
        $nbpage=ceil($nbproduit/$nbr);
        $prod = $repository->findBy([],[],$nbr,($page - 1)*$nbr);

        return $this->render('produit/listpag.html.twig', [
            'produit' => $prod,
            'ispaginated'=> true,
            'nbpage'=>$nbpage,
            'page'=>$page,
            'nbr'=>$nbr
        ]);
    }

    //retour depuis twig stars value
    #[Route('/star/{id}', name: 'star')]
    public function yourAction(HttpFoundationRequest $request,$id,ManagerRegistry $doctrine)
    {
        if ($request->isXmlHttpRequest()) {
            // handle the AJAX request
            $data = $request->getContent(); // retrieve the data sent by the client-side JavaScript code
            $repository = $doctrine->getRepository(produit::class);
            $produits = $repository->find($id);
            $produits->setNote(($produits->getNote()+$data[6])/2);//modifier la note du produit
            $em=$doctrine->getManager();
            $em->persist($produits);
            $em->flush();
            $prod = $repository->find($id);
            $test=$prod->getNote();
            $response = new Response();//nouvelle instance du response pour la renvoyer a la fonction ajax
            $response->setContent(json_encode($test));//encoder les donnes sous forme JSON et les attribuer a la variable response
            $response->headers->set('Content-Type', 'application/json');
            return $response;//envoie du response
        } 
    }

    #[Route('/catprod/{id}', name: 'prodbycat')]
    public function show_prodcat($id,ProduitRepository $rep, PaginatorInterface $pagination,HttpFoundationRequest $request ): Response
    {
        //$produits = $rep->Findprodbycat($id);
        $prod=$pagination->paginate(
            $produits = $rep->Findprodbycat($id),
            $request->query->getInt('page',1),
            //$nb=count($produits),
            3,
        );
        return $this->render('produit/listpf.html.twig', [
            'produit' => $prod,
            'id' => $id,
        ]);
    }
}
