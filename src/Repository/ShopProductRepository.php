<?php

namespace App\Repository;

use App\Entity\ShopProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
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
     * @param array $parameters
     *
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCountProductsByParameters(array $parameters): int
    {
        $queryBuilder = $this->getProductsByParameters($parameters);

        $countProducts = $queryBuilder
            ->select('COUNT(s.id)');

        return (int)$countProducts
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getProductsWithLimitAndOffsetAndCountItems(array $parameters): array {
        $limit = $parameters['limit'];
        $offset = $parameters['offset'];

        $queryBuilder = $this->getProductsByParameters($parameters);

        $items = $queryBuilder->setFirstResult($offset)
                              ->setMaxResults($limit);

        return $items
            ->getQuery()
            ->getResult();
    }

    public function getLatestProducts(): array
    {
        $queryBuilder = $this->createQueryBuilder('sp')
            ->where('sp.quantity > 0')
            ->andWhere('sp.enable = 1')
            ->orderBy('sp.id', 'DESC')
            ->setMaxResults(8);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    private function getProductsByParameters(array $parameters): QueryBuilder {
        $sortBy = $parameters['sortBy'];
        $sortOrder = $parameters['sortOrder'];
        $brandSlug = $parameters['brand'];
        $colorSlug = $parameters['color'];
        $priceFrom = $parameters['priceFrom'];
        $priceTo = $parameters['priceTo'];
        $categorySlug = $parameters['categorySlug'];

        $queryBuilder = $this->createQueryBuilder('s')
                             ->where('s.enable = 1')
                             ->orderBy('s.id', 'DESC');

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

        if($priceFrom) {
            $queryBuilder
                ->andWhere('s.priceGross >= :priceFrom')
                ->setParameter('priceFrom', $priceFrom);
        }

        if($priceTo) {
            $queryBuilder
                ->andWhere('s.priceGross <= :priceTo')
                ->setParameter('priceTo', $priceTo);
        }

        if($categorySlug) {
            $queryBuilder
                ->leftJoin('s.shopCategory', 'shopCategory')
                ->andWhere('shopCategory.slug = :shopCategorySlug')
                ->setParameter('shopCategorySlug', $categorySlug);
        }

        return $queryBuilder;
    }

    public function findByNameLike(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('sp')
            ->where('sp.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->setMaxResults(4);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $uuid
     *
     * @return ShopProduct|null
     *
     * @throws NonUniqueResultException
     */
    public function findActiveByUuid(string $uuid): ?ShopProduct
    {
        $queryBuilder = $this->createQueryBuilder('sp')
            ->where('sp.enable = 1')
            ->andWhere('sp.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }
}
