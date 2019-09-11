<?php

namespace App\Repository;

use App\Entity\Validations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Validations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Validations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Validations[]    findAll()
 * @method Validations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValidationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Validations::class);
    }

    // /**
    //  * @return Validations[] Returns an array of Validations objects
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
    public function findOneBySomeField($value): ?Validations
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
