<?php

namespace App\Repository;

use App\Entity\ShopProductSpecification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShopProductSpecification|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopProductSpecification|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopProductSpecification[]    findAll()
 * @method ShopProductSpecification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopProductSpecificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopProductSpecification::class);
    }
}
