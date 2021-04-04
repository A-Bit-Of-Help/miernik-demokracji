<?php

namespace App\Repository;

use App\Entity\GovernmentParties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GovernmentParties|null find($id, $lockMode = null, $lockVersion = null)
 * @method GovernmentParties|null findOneBy(array $criteria, array $orderBy = null)
 * @method GovernmentParties[]    findAll()
 * @method GovernmentParties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GovernmentPartiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GovernmentParties::class);
    }

    // /**
    //  * @return GovernmentParties[] Returns an array of GovernmentParties objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GovernmentParties
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
