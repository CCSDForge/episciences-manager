<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;


/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findAllForList(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.rvid', 'r.code', 'r.status', 'r.name', 'r.is_new_front_switched')
            ->getQuery()
            ->getResult();
    }


    /**
     * Create a QueryBuilder for active reviews with new front switched on.
     *
     * @return QueryBuilder
     */
    public function findActiveNewFrontReviewsQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->select('r.rvid', 'r.code', 'r.status', 'r.name', 'r.is_new_front_switched')
            ->where('r.rvid != :zero')
            ->andWhere('r.is_new_front_switched = :isNewFront')
            ->setParameter('zero', 0)
            ->setParameter('isNewFront', true)
            ->orderBy('r.name', 'ASC');
    }

    /**
     * Find all reviews that are active and have the new front switched on and not the default rvid (0).
     *
     * @return array
     */
    public function findActiveNewFrontReviews(): array
    {
        return $this->findActiveNewFrontReviewsQuery()
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Review[] Returns an array of Review objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Review
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
