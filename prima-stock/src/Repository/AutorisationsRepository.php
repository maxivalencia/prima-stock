<?php

namespace App\Repository;

use App\Entity\Autorisations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Autorisations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Autorisations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Autorisations[]    findAll()
 * @method Autorisations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorisationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Autorisations::class);
    }

    // /**
    //  * @return Autorisations[] Returns an array of Autorisations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Autorisations
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
