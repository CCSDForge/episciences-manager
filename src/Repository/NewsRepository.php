<?php
namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /** @return list<News> */
    public function findByRvcode(string $rvcode): array
    {
        return $this->queryByRvcode($rvcode)->getQuery()->getResult();
    }

    public function queryByRvcode(string $rvcode): QueryBuilder
    {
        return $this->createQueryBuilder('n')
            ->where('n.rvcode = :rvcode')
            ->setParameter('rvcode', $rvcode)
            ->orderBy('n.dateCreation', 'DESC');
    }

    /**
     * Find news that contain a specific filename in their content (JSON column)
     * Uses SQL LIKE for efficient filtering before loading entities
     *
     * @return list<News>
     */
    public function findByContentContaining(string $filename, string $rvcode): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.rvcode = :rvcode')
            ->andWhere('n.content LIKE :filename')
            ->setParameter('rvcode', $rvcode)
            ->setParameter('filename', '%' . $filename . '%')
            ->getQuery()
            ->getResult();
    }
}
