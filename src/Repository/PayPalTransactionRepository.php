<?php

namespace App\Repository;

use App\Entity\PayPalTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PayPalTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method PayPalTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method PayPalTransaction[]    findAll()
 * @method PayPalTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayPalTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayPalTransaction::class);
    }

    /**
     * @param string $token
     *
     * @return PayPalTransaction|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneByToken(string $token): ?PayPalTransaction
    {
        $queryBuilder = $this->createQueryBuilder('t')
                             ->where('t.token = :token')
                             ->setParameter('token', $token)
                             ->setMaxResults(1);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }
}
