<?php

namespace App\Repository;

use App\Entity\Mouvements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Mouvements|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mouvements|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mouvements[]    findAll()
 * @method Mouvements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouvements::class);
    }

    // /**
    //  * @return Mouvements[] Returns an array of Mouvements objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mouvements
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
