<?php

namespace App\Repository;

use App\Entity\ClientUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientUser[]    findAll()
 * @method ClientUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientUser::class);
    }

    /**
     * @param string $email
     *
     * @return ClientUser|null
     *
     * @throws NonUniqueResultException
     */
    public function findByEmail(string $email): ?ClientUser
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }
}
