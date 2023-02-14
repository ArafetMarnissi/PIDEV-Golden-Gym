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
        return $this->render('produit/listp.html.twig', [
            'produit' => $produits,
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

    #[Route('/get/{id}', name: 'getid')]
    public function show_id(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(produit::class);
        $produits = $repository->find($id);
        return $this->render('produit/detail.html.twig', [
            'produits' => $produits,
            'id' => $id,
        ]);
    }

    #[Route('/listpf', name: 'list_produit_front')]
    public function listpf(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Produit::class);
        $produits = $repository->findAll();
        return $this->render('produit/listpf.html.twig', [
            'produit' => $produits,
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
    

}
