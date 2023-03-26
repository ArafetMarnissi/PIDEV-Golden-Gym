<?php

namespace App\Repository;

use App\Entity\Abonnement;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Abonnement>
 *
 * @method Abonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abonnement[]    findAll()
 * @method Abonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnement::class);
    }

    public function save(Abonnement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Abonnement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function order_By_PRIX_ASC()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.prixAbonnement', 'ASC')
            ->getQuery()->getResult();
    }

    public function order_By_PRIX_desc()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.prixAbonnement', 'DESC')
            ->getQuery()->getResult();
    }    

    public function findMostSoldAbonnement($limit = 6)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.nomAbonnement', 'p.dureeAbonnement', 'COUNT(s.id) AS total_reservation')
            ->join('p.reservation', 's')
            ->groupBy('p.id')
            ->orderBy('total_reservation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery();
    
        return $qb->getResult();
    }

    // public function calcul(){

    //     $qb = $this->createQueryBuilder('p')
    //     ->select('p.nomAbonnement', 'p.dureeAbonnement', 'COUNT(s.id) AS total_reservation')
    //     ->join('p.reservation', 's')
    //     ->groupBy('p.id')
    //     ->getQuery();
    // }

//    /**
//     * @return Abonnement[] Returns an array of Abonnement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Abonnement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
