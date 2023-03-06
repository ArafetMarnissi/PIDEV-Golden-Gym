<?php
namespace App\service;

use App\Repository\CommandeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ServiceCommande{


    public function __construct(
        private RequestStack $requestStack,
        private CommandeRepository $commandeRepository,
        private PaginatorInterface $paginatorInterface
        ) {
        
    }
public function getPaginetCommande()
{
    $request = $this->requestStack->getMainRequest();
    $page = $request->query->getInt('page',1);
    $limit=5;

    $commandeQuery = $this->commandeRepository->createQueryBuilder('c')
    ->orderBy('c.dateCommande', 'DESC')
    ->getQuery();
    return $this->paginatorInterface->paginate($commandeQuery,$page,$limit);
}

public function getPaginetCommandeClient($userId)
{
    $request = $this->requestStack->getMainRequest();
    $page = $request->query->getInt('page',1);
    $limit=5;

    $commandeQuery = $this->commandeRepository->findByUserId($userId);
    return $this->paginatorInterface->paginate($commandeQuery,$page,$limit);
}

}