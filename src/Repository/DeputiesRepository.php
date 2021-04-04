<?php

namespace App\Repository;

use App\Entity\Deputies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deputies|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deputies|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deputies[]    findAll()
 * @method Deputies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeputiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deputies::class);
    }

    // /**
    //  * @return Deputies[] Returns an array of Deputies objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Deputies
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
