<?php
namespace App\Repository;

use App\Entity\IndexingDatabase;
use App\Enum\IndexingDatabaseStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IndexingDatabase>
 */
class IndexingDatabaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndexingDatabase::class);
    }

    /**
     * @return list<IndexingDatabase>
     */
    public function findAllValidated(): array
    {
        return $this->createQueryBuilder('idb')
            ->where('idb.status = :status')
            ->setParameter('status', IndexingDatabaseStatus::VALIDATED)
            ->orderBy('idb.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Query pending proposals ordered by creation date (oldest first - FIFO for admin processing).
     */
    public function queryPending(): QueryBuilder
    {
        return $this->createQueryBuilder('idb')
            ->where('idb.status = :status')
            ->setParameter('status', IndexingDatabaseStatus::PENDING)
            ->orderBy('idb.createdAt', 'ASC');
    }

    /**
     * @return list<IndexingDatabase>
     */
    public function findPending(): array
    {
        return $this->queryPending()
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pending proposals with newest first (for journal managers to see their latest proposals).
     *
     * @return list<IndexingDatabase>
     */
    public function findPendingNewestFirst(): array
    {
        return $this->createQueryBuilder('idb')
            ->where('idb.status = :status')
            ->setParameter('status', IndexingDatabaseStatus::PENDING)
            ->orderBy('idb.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<IndexingDatabase>
     */
    public function findRejected(): array
    {
        return $this->createQueryBuilder('idb')
            ->where('idb.status = :status')
            ->setParameter('status', IndexingDatabaseStatus::REJECTED)
            ->orderBy('idb.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('idb')
            ->orderBy('idb.name', 'ASC');
    }

    public function queryAllValidated(): QueryBuilder
    {
        return $this->createQueryBuilder('idb')
            ->where('idb.status = :status')
            ->setParameter('status', IndexingDatabaseStatus::VALIDATED)
            ->orderBy('idb.name', 'ASC');
    }

    /**
     * @return list<IndexingDatabase>
     */
    public function findByReview(int $rvid): array
    {
        return $this->createQueryBuilder('idb')
            ->innerJoin('idb.reviews', 'r')
            ->where('r.rvid = :rvid')
            ->andWhere('idb.status = :status')
            ->setParameter('rvid', $rvid)
            ->setParameter('status', IndexingDatabaseStatus::VALIDATED)
            ->orderBy('idb.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
