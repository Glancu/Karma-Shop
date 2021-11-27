<?php

namespace App\Repository;

use App\Entity\ShopBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShopBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopBrand[]    findAll()
 * @method ShopBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopBrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopBrand::class);
    }
}
