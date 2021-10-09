<?php

namespace App\Repository;

use App\Entity\EmailTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailTemplate[]    findAll()
 * @method EmailTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailTemplate::class);
    }

    /**
     * @param int $type
     *
     * @return EmailTemplate|null
     *
     * @throws NonUniqueResultException
     */
    public function findByType(int $type): ?EmailTemplate
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->where('e.type = :type')
            ->setParameter('type', $type);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }
}
