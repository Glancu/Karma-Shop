<?php

namespace App\Repository;

use App\Entity\BlogTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogTag[]    findAll()
 * @method BlogTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogTag::class);
    }

    public function getNamesList(): array
    {
        $queryBuilder = $this->createQueryBuilder('bt')
                             ->select('bt.name, bt.slug, bt.uuid')
                             ->groupBy('bt.name');

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
