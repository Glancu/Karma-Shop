<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByClientEmail(string $clientId): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
                             ->leftJoin('o.user', 'user')
                             ->where('user.email = :userEmail')
                             ->setParameter('userEmail', $clientId)
                             ->orderBy('o.id', 'DESC');

        $query = $queryBuilder->getQuery();

        $query->setCacheable(true);

        return $query
            ->getResult();
    }

    /**
     * @param string $uuid
     *
     * @return Order|null
     *
     * @throws NonUniqueResultException
     */
    public function findByUuid(string $uuid): ?Order
    {
        $queryBuilder = $this->createQueryBuilder('o')
                             ->where('o.uuid = :uuid')
                             ->setParameter('uuid', $uuid)
                             ->setMaxResults(1);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }
}
