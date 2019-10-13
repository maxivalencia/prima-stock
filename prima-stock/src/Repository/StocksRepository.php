<?php

namespace App\Repository;

use App\Entity\Stocks;
use App\Entity\Produits;
use App\Entity\Projet;
use App\Entity\Clients;
use App\Repository\ProduitsRepository;
use App\Repository\ProjetRepository;
use App\Repository\ClientsRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

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


    /**
     * @return Stocks[] Returns an array of Stocks objects
     */    
    public function findRecherche($recherche, $produits1, $produits2, $projet, $client)
    {
        $myDateTime = new DateTime($recherche);
        return $this->createQueryBuilder('s')
            ->Where('s.referencePanier LIKE :val1')
            ->orWhere('s.produit = :val2')
            ->orWhere('s.produit = :val3')
            ->orWhere('s.projet = :val4')
            ->orWhere('s.client = :val5')
            ->orWhere('s.dateSaisie BETWEEN :val6 AND :val7')
            ->orWhere('s.dateValidation BETWEEN :val6 AND :val7')
            //->setParameter('val', $value)
            ->setParameter('val1', '%'.$recherche.'%')
            ->setParameter('val2', $produits1)
            ->setParameter('val3', $produits2)
            ->setParameter('val4', $projet)
            ->setParameter('val5', $client)
            //->setParameter('val6', $myDateTime)
            //->setParameter('val7', $myDateTime)
            //->andWhere('d.objet LIKE :val4 OR d.daterecepeffectif BETWEEN  :val5 AND :val6')
            ->setParameter('val6', date_create($recherche.' 00:00:00'))
            ->setParameter('val7', date_create($recherche.' 23:59:59'))
            ->groupBy('s.referencePanier')
            ->orderBy('s.id', 'DESC')
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
