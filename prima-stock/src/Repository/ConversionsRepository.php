<?php

namespace App\Repository;

use App\Entity\Conversions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Conversions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversions[]    findAll()
 * @method Conversions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversions::class);
    }

    // /**
    //  * @return Conversions[] Returns an array of Conversions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Conversions
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
