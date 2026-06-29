<?php

namespace App\Repository;

use App\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }
    
    public function findOneByToken($token)
    {
        $token = trim($token,'/ ');
        if (empty($token)) {
            $path = '/';
        }else{
            $path = '/'.$token.'/';
        }
        return $this->findOneBy(['path'=>$path]);
    }
    
    public function findOnePublishedByToken($token)
    {
        $token = trim($token,'/ ');
        if (empty($token)) {
            $path = '/';
        }else{
            $path = '/'.$token.'/';
        }
        return $this->findOneBy(['path'=>$path,'published'=>true]);
    }

    public function findPublishedByModel(int $brandId, int $modelId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.published = :published')
            ->andWhere('c.brand_id = :brand')
            ->andWhere('c.model_id = :model')
            ->setParameter('published', true)
            ->setParameter('brand', $brandId)
            ->setParameter('model', $modelId)
            ->orderBy('c.sort', 'ASC')
            ->getQuery()
            ->getResult();
    }
    

    // /**
    //  * @return Content[] Returns an array of Content objects
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
    public function findOneBySomeField($value): ?Content
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
