<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
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


        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'items' => $panierWithData,
            'total' => $total
        ]);
    }
    #[Route('/panier/add/{id}', name: 'cart_add')]
    public function add($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("app_panier");
    }
    #[Route('/panier/remove{id}', name: 'removePrPa')]
    public function remove($id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("app_panier");
    }

    #[Route('/panier/moins/{id}', name: 'panierMoins')]
    public function moins($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (($panier[$id] >= 2)) {
            $panier[$id]--;
        }
        // else {
        //     unset($panier[$id]);
        // }
        $session->set('panier', $panier);

        return $this->redirectToRoute("app_panier");
    }
    #[Route('/panier/plus/{id}', name: 'panierPlus')]
    public function plus($id, SessionInterface $session, ProduitRepository $produitRepository)
    {
        $panier = $session->get('panier', []);
        $product = $produitRepository->find($id);
        if (!empty($panier[$id]) && ($product->getQuantiteProduit() - $panier[$id]) > 0) {
            $panier[$id]++;
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("app_panier");
    }
}
