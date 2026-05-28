<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

//    /**
//     * @return Page[] Returns an array of Page objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Page
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Find pages that contain a specific filename in their content (JSON column)
     * Uses SQL LIKE for efficient filtering before loading entities
     *
     * @return list<Page>
     */
    public function findByContentContaining(string $filename, string $rvcode): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.rvcode = :rvcode')
            ->andWhere('p.content LIKE :filename')
            ->setParameter('rvcode', $rvcode)
            ->setParameter('filename', '%' . $filename . '%')
            ->getQuery()
            ->getResult();
    }
}
