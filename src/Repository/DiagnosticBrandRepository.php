<?php

namespace App\Repository;

use App\Entity\DiagnosticBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DiagnosticBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiagnosticBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiagnosticBrand[]    findAll()
 * @method DiagnosticBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiagnosticBrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiagnosticBrand::class);
    }

    // /**
    //  * @return DiagnosticBrand[] Returns an array of DiagnosticBrand objects
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
    public function findOneBySomeField($value): ?DiagnosticBrand
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
