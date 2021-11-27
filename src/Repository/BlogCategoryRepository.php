<?php

namespace App\Repository;

use App\Entity\BlogCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogCategory[]    findAll()
 * @method BlogCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogCategory::class);
    }

    public function getItemsByLimit(int $limit): array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        $query->setCacheable(true);

        return $query
            ->getResult();
    }

    public function getNamesWithCount(): array
    {
        $queryBuilder = $this->createQueryBuilder('bc')
            ->leftJoin('bc.posts', 'post')
            ->select('bc.name, bc.slug, bc.uuid')
            ->addSelect('COUNT(post.id) as countPosts')
            ->where('post.enable = 1')
            ->groupBy('bc.name');

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
