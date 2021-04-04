<?php

namespace App\Repository;

use App\Entity\VotesResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VotesResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method VotesResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method VotesResult[]    findAll()
 * @method VotesResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VotesResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VotesResult::class);
    }

    // /**
    //  * @return VotesResult[] Returns an array of VotesResult objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VotesResult
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
