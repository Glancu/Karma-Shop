<?php

namespace App\Repository;

use App\Entity\ShopColor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShopColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopColor[]    findAll()
 * @method ShopColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopColorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopColor::class);
    }

    // /**
    //  * @return ShopColor[] Returns an array of ShopColor objects
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
    public function findOneBySomeField($value): ?ShopColor
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
