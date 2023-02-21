<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/listcat', name: 'list_cat')]
    public function listc(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Category::class);
        $produits=$repository->findAll();
        return $this->render('category/listc.html.twig', [
            'produit' => $produits,
        ]);
    }

    #[Route('/addcat',name:'addcat')]
    public function addcat (HttpFoundationRequest $request,ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        //$repository= $doctrine->getRepository(Produit::class);
        //$produits=$repository->findAll();
        $produit=new Category;
        $form=$this->createForm(CategoryType::class,$produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() )
        {
            $brochureFile = $form->get('imageCategorie')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('categorie_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $produit->setImageCategorie($newFilename);
            }
            $em=$doctrine->getManager();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('list_cat');
        }
        return $this->renderForm('category/newcat.html.twig',['formc'=>$form,"editmode"=>$produit->getid()!==null]);
    }

    #[Route('/deletecat/{id}',name: 'deletecat')]
    public function deletecat (ManagerRegistry $doctrine,$id):Response
    {  
        $repository=$doctrine->getRepository(Category::class);
        $produit=$repository->find($id);
        $em=$doctrine->getManager();
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('list_cat');
    }

    #[Route('/editcat/{id}', name: 'editcat')]
    public function editcat(HttpFoundationRequest $request,ManagerRegistry $doctrine,$id,SluggerInterface $slugger ): Response
    {  
        $repository= $doctrine->getRepository(Category::class);
        $produits=$repository->find($id);
       $form=$this->createForm(CategoryType::class,$produits);
       $form->handleRequest($request);
       if($form->isSubmitted())
       {
        $brochureFile = $form->get('imageCategorie')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('categorie_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $produits->setImageCategorie($newFilename);
            }
        $em=$doctrine->getManager();
        $produits->setNomCategory($form->get('nomCategory')->getData());
        $em->flush();
        return $this->redirectToRoute('list_cat');
       }
       return $this->renderForm('category/newcat.html.twig',['formc'=>$form,"editmode"=>$produits->getid()!==null]);
    }

    #[Route('/getcatf/{id}', name: 'gtcatf')]
    public function show_catf(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Category::class);
        $categorie = $repository->find($id);
        return $this->render('category/listprodcat.html.twig', [
            'cat' => $categorie,
            'id' => $id,
        ]);
    }

    #[Route('/listcf', name: 'listcf')]
    public function listcf(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Category::class);
        $produits=$repository->findAll();
        return $this->render('category/listcatfront.html.twig', [
            'produit' => $produits,
        ]);
    }
}
