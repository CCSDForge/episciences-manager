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
}
