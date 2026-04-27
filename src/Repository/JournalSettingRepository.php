<?php

namespace App\Repository;

use App\Entity\JournalSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JournalSetting>
 */
class JournalSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalSetting::class);
    }

    public function findByRvid(int $rvid): ?JournalSetting
    {
        return $this->findOneBy(['rvid' => $rvid]);
    }

    public function findByCode(?string $code = null): ?JournalSetting
    {
        return $this->findOneBy(['code' => $code]);
    }
}
