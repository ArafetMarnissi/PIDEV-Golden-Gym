<?php

namespace App\Repository;

use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participation>
 *
 * @method Participation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participation[]    findAll()
 * @method Participation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    public function save(Participation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Participation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Participation[] Returns an array of Participation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Participation
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function FindPartById($activite,$user)
{
    $entityManager=$this->getEntityManager();
    $query=$entityManager->createQuery("SELECT p FROM App\Entity\Participation p WHERE p.activite=:activite AND p.User=:user")
    ->setParameter('activite',$activite)
    ->setParameter('user',$user);
    return $query->getOneOrNullResult();
}
/*public function FindPartsById($user)
{
    $entityManager=$this->getEntityManager();
    $query=$entityManager->createQuery("SELECT p FROM App\Entity\Participation p WHERE p.User=:user")
    ->setParameter('user',$user);
    return $query->getResult();
}*/

public function FindPartsById($user)
{
    $entityManager=$this->getEntityManager();
    $query=$entityManager->createQuery("SELECT p FROM App\Entity\Participation p JOIN p.activite a WHERE a.DateActivite>=CURRENT_DATE() and p.User=:user")
    ->setParameter('user',$user);
    return $query->getResult();
}
}
