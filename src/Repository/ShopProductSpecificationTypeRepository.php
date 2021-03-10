<?php

namespace App\Repository;

use App\Entity\ShopProductSpecificationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShopProductSpecificationType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopProductSpecificationType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopProductSpecificationType[]    findAll()
 * @method ShopProductSpecificationType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopProductSpecificationTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopProductSpecificationType::class);
    }

    // /**
    //  * @return ShopProductSpecificationType[] Returns an array of ShopProductSpecificationType objects
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
    public function findOneBySomeField($value): ?ShopProductSpecificationType
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
