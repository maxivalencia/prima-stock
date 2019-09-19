<?php

namespace App\Repository;

use App\Entity\Stocks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Stocks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stocks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stocks[]    findAll()
 * @method Stocks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StocksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stocks::class);
    }


    /**
     * @return Stocks[] Returns an array of Stocks objects
     */    
    public function findByGroup($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.etat = :val')
            ->setParameter('val', $value)
            ->groupBy('s.referencePanier')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Stocks[] Returns an array of Stocks objects
     */    
    public function findByGroupAutre($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.etat = :val')
            ->setParameter('val', $value)
            ->groupBy('s.referencePanier')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Stocks[] Returns an array of Stocks objects
     */    
    public function findGroupValidation($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.referencePanier = :val')
            ->setParameter('val', $value)
            //->groupBy('s.referencePanier')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Stocks[] Returns an array of Stocks objects
     */    
    public function findProduction()
    {
        return $this->createQueryBuilder('s')
            //->andWhere('s.referencePanier = :val')
            //->setParameter('val', $value)
            ->groupBy('s.referencePanier')
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Stocks[] Returns an array of Stocks objects
     */    
    public function findTotal($value1 = null, $value2 = null, $value3 = null)
    {
        //à trouver le total des produits
        return $this->createQueryBuilder('s')
            ->andWhere('s.produit = :val1')
            ->andWhere('s.projet = :val2')
            ->andWhere('s.etat = :val3')
            ->setParameter('val1', $value1)
            ->setParameter('val2', $value2)
            ->setParameter('val3', $value3)
            //->groupBy('s.referencePanier')
            //->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Stocks[] Returns an array of Stocks objects
     */    
    public function findEtat()
    {
        //à trouver le total des produits
        return $this->createQueryBuilder('s')
            //->andWhere('s.etat = :val1')
            //->setParameter('val1', $value1)
            ->groupBy('s.produit')
            //->groupBy('s.mouvement')
            //->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Stocks[] Returns an array of Stocks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Stocks
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
