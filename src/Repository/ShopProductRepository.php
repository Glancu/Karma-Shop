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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getProductsWithLimitAndOffsetAndCountItems(array $parameters): array {
        $limit = $parameters['limit'] ?? 10;
        $offset = $parameters['offset'] ?? 0;
        $sortBy = $parameters['sortBy'] ?? null;
        $sortOrder = $parameters['sortOrder'] ?? 'DESC';
        $brandSlug = $parameters['brandSlug'] ?? null;
        $colorSlug = $parameters['colorSlug'] ?? null;

        $queryBuilder = $this->createQueryBuilder('s')
                             ->where('s.enable = 1')
                             ->orderBy('s.id', 'DESC')
                             ->setFirstResult($offset)
                             ->setMaxResults($limit);

        if ($sortBy && $sortBy !== 'newset') {
            $queryBuilder
                ->orderBy("s.${sortBy}", $sortOrder);
        }

        if ($brandSlug) {
            $queryBuilder
                ->leftJoin('s.shopBrand', 'brand')
                ->andWhere('brand.slug = :brandSlug')
                ->setParameter('brandSlug', $brandSlug)
            ;
        }

        if ($colorSlug) {
            $queryBuilder
                ->leftJoin('s.shopColors', 'color')
                ->andWhere('color.slug = :colorSlug')
                ->setParameter('colorSlug', $colorSlug)
            ;
        }

        $items = $queryBuilder
            ->getQuery()
            ->getResult();

        $countProducts = $queryBuilder
            ->select('COUNT(s.id)')
            ->setFirstResult(0)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'items' => $items,
            'countProducts' => (int)$countProducts
        ];
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
