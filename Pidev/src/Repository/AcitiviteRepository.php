<?php

namespace App\Repository;

use App\Entity\Activite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Acitivite>
 *
 * @method Acitivite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Acitivite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Acitivite[]    findAll()
 * @method Acitivite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcitiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Acitivite::class);
    }

    public function save(Activite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Activite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Acitivite[] Returns an array of Acitivite objects
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

//    public function findOneBySomeField($value): ?Acitivite
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function TriParNomActivite()
{
    $entityManager=$this->getEntityManager();
    $query=$entityManager->createQuery('SELECT p FROM App\Entity\Activite p ORDER BY p.nomAcitivite ASC');
    return $query->getResult();
}

public function TriParDateActivite()
{
    $entityManager=$this->getEntityManager();
    $query=$entityManager->createQuery('SELECT p FROM App\Entity\Activite p ORDER BY p.DateActivite ASC');
    return $query->getResult();
}
}
