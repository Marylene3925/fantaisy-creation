<?php

namespace App\Repository;

use App\Entity\Colory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Colory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Colory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Colory[]    findAll()
 * @method Colory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColoryRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Colory::class);
    }

    // /**
    //  * @return Colory[] Returns an array of Colory objects
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
    public function findOneBySomeField($value): ?Colory
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
