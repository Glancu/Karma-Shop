<?php

namespace App\Repository;

use App\Entity\ShopProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShopProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopProduct[]    findAll()
 * @method ShopProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopProduct::class);
    }

    public function getProductsWithLimitAndOffset($limit = 10, $offset = 0): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
                             ->where('s.enable = 1')
                             ->orderBy('s.id', 'DESC')
                             ->setFirstResult($offset)
                             ->setMaxResults($limit);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function getCountEnableProducts(): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
                             ->select('COUNT(s.id)')
                             ->where('s.enable = 1');

        try {
            return $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }
}
