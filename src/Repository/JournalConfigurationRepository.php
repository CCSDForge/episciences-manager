<?php

namespace App\Repository;

use App\Entity\JournalConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JournalConfiguration>
 */
class JournalConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalConfiguration::class);
    }

    public function findByRvid(int $rvid): ?JournalConfiguration
    {
        return $this->findOneBy(['rvid' => $rvid]);
    }
}