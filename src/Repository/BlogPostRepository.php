<?php

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @param BlogPost $blogPost
     *
     * @throws Exception
     */
    public function update(BlogPost $blogPost): void
    {
        try {
            $this->_em->persist($blogPost);
            $this->_em->flush();
        } catch (Exception $e) {
            throw new Exception('An Error occured during save: ' . $e->getMessage());
        }
    }

    /**
     * @param array $parameters
     *
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCountPostsByParameters(array $parameters): int
    {
        $queryBuilder = $this->getPostsByParameters($parameters);

        $countProducts = $queryBuilder
            ->select('COUNT(s.id)');

        return (int)$countProducts
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPostsWithLimitAndOffsetAndCountItems(array $parameters): array
    {
        $queryBuilder = $this->getPostsByParameters($parameters);

        $items = $queryBuilder->setFirstResult($parameters['offset'])
                              ->setMaxResults($parameters['limit']);

        return $items
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $slug
     *
     * @return BlogPost|null
     *
     * @throws NonUniqueResultException
     */
    public function findBySlug(string $slug): ?BlogPost
    {
        $queryBuilder = $this->createQueryBuilder('p')
                             ->where('p.enable = 1')
                             ->andWhere('p.slug = :slug')
                             ->setParameter('slug', $slug);

        $query = $queryBuilder->getQuery();

        $query->setCacheable(true);

        return $query
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNextAndPreviousPostsIsById(int $id): array
    {
        $expr = $this->_em->getExpressionBuilder();

        $next = $this->createQueryBuilder('a')
                     ->select($expr->min('a.id'))
                     ->where($expr->gt('a.id', ':id'))
                     ->andWhere('a.enable = 1');

        $previous = $this->createQueryBuilder('b')
                         ->select($expr->max('b.id'))
                         ->where($expr->lt('b.id', ':id'))
                         ->andWhere('b.enable = 1');

        $query = $this->createQueryBuilder('o')
                      ->select('COUNT(o.id) as total')
                      ->addSelect('(' . $previous->getDQL() . ') as previous')
                      ->addSelect('(' . $next->getDQL() . ') as next')
                      ->setParameter('id', $id)
                      ->getQuery();

        return $query->getSingleResult();
    }

    public function getPopularPosts($limit = 4): array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->orderBy('b.views' , 'DESC')
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        $query->setCacheable(true);

        return $query
            ->getResult();
    }

    /**
     * @param string $uuid
     *
     * @return BlogPost|null
     *
     * @throws NonUniqueResultException
     */
    public function findActiveByUuid(string $uuid): ?BlogPost
    {
        $queryBuilder = $this->createQueryBuilder('bp')
            ->where('bp.enable = 1')
            ->andWhere('bp.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        $query = $queryBuilder->getQuery();

        $query->setCacheable(true);

        return $query
            ->getOneOrNullResult();
    }

    public function findByTitleLike(string $title): array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->where('b.enable = 1')
            ->andWhere('b.title LIKE :title')
            ->setParameter('title', "%${title}%");

        $query = $queryBuilder->getQuery();

        $query->setCacheable(true);

        return $query
            ->getResult();
    }

    private function getPostsByParameters(array $parameters): QueryBuilder
    {
        $category = $parameters['category'];
        $tag = $parameters['tag'];

        $queryBuilder = $this->createQueryBuilder('s')
                             ->where('s.enable = 1')
                             ->orderBy('s.id', 'DESC');

        if ($category) {
            $queryBuilder
                ->leftJoin('s.category', 'category')
                ->andWhere('category.slug = :category')
                ->setParameter('category', $category);
        }

        if ($tag) {
            $queryBuilder
                ->leftJoin('s.tags', 'tag')
                ->andWhere('tag.slug = :tagSlug')
                ->setParameter('tagSlug', $tag);
        }

        return $queryBuilder;
    }
}
