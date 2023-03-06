<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Twilio\Rest\Client;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function Findprodbycat($n)
    {
        $entityManager=$this->getEntityManager();
        $query=$entityManager->createQuery("SELECT p FROM App\Entity\Produit p JOIN p.category c WHERE c.id=:n")->setParameter('n',$n);
        return $query->getResult();
    }
    public  function sms(string $quant){
        // Your Account SID and Auth Token from twilio.com/console
                $sid = 'ACc210d7a671c53bca6b98f6a1bec72d25';
                $auth_token = '44d399438ff391243bcf90d8a00607fd';
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
        // A Twilio number you own with SMS capabilities
                $twilio_number = "+12762849300";
        
                $client = new Client($sid, $auth_token);
                $client->messages->create(
                // the number you'd like to send the message to
                    '+21693763578',
                    [
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => '++12763303927',
                        // the body of the text message you'd like to send
                        'body' => 'Le produit avec le nom: '. $quant .'va bientot expire'
                    ]
                );
            }
            public  function sms1(string $quant){
                // Your Account SID and Auth Token from twilio.com/console
                        $sid = 'ACc210d7a671c53bca6b98f6a1bec72d25';
                        $auth_token = '44d399438ff391243bcf90d8a00607fd';
                // In production, these should be environment variables. E.g.:
                // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
                // A Twilio number you own with SMS capabilities
                        $twilio_number = "+12762849300";
                
                        $client = new Client($sid, $auth_token);
                        $client->messages->create(
                        // the number you'd like to send the message to
                            '+21693763578',
                            [
                                // A Twilio phone number you purchased at twilio.com/console
                                'from' => '++12763303927',
                                // the body of the text message you'd like to send
                                'body' => 'Le produit avec le nom "'. $quant .'" a expire'
                            ]
                        );
                    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
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

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
