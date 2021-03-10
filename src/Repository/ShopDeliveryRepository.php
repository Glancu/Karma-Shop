<?php

namespace App\Repository;

use App\Entity\ShopDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShopDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopDelivery[]    findAll()
 * @method ShopDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopDelivery::class);
    }

    // /**
    //  * @return ShopDelivery[] Returns an array of ShopDelivery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShopDelivery
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
