<?php

namespace App\Repository;

use App\Entity\JournalFrontendSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JournalFrontendSetting>
 */
class JournalFrontendSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalFrontendSetting::class);
    }

    public function findByRvid(int $rvid): ?JournalFrontendSetting
    {
        return $this->findOneBy(['rvid' => $rvid]);
    }
}