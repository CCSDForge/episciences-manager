<?php
namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findAll(): array
    {
        return $this->createQueryBuilder('n')
            ->select( 'n.code', 'n.date_creation', 'n.title', 'n.content', 'n.link', 'n.visibility')
            ->getQuery()
            ->getResult();
    }

    public function findByJournalId(int $rvid): array
    {
        return $this->findBy(['rvid' => $rvid], ['createdAt' => 'DESC']);
    }
}
