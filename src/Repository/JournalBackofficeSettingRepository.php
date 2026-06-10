<?php

namespace App\Repository;

use App\Entity\JournalBackofficeSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JournalBackofficeSetting>
 */
class JournalBackofficeSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalBackofficeSetting::class);
    }

    /**
     * Find all settings for a journal.
     *
     * @return JournalBackofficeSetting[]
     */
    public function findByRvid(int $rvid): array
    {
        return $this->findBy(['rvid' => $rvid]);
    }

    /**
     * Find a specific setting for a journal.
     */
    public function findOneSetting(int $rvid, string $setting): ?JournalBackofficeSetting
    {
        return $this->findOneBy(['rvid' => $rvid, 'setting' => $setting]);
    }

    /**
     * Get all settings for a journal as an associative array.
     *
     * @return array<string, string|null>
     */
    public function getSettingArray(int $rvid): array
    {
        $settings = $this->findByRvid($rvid);
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->getSetting()] = $setting->getValue();
        }

        return $result;
    }

    /**
     * Update or create a setting.
     */
    public function updateSetting(int $rvid, string $key, ?string $value): void
    {
        $setting = $this->findOneSetting($rvid, $key);

        if ($setting === null) {
            $setting = new JournalBackofficeSetting();
            $setting->setRvid($rvid);
            $setting->setSetting($key);
            $this->getEntityManager()->persist($setting);
        }

        $setting->setValue($value);
        $this->getEntityManager()->flush();
    }

    /**
     * Update multiple settings at once.
     *
     * @param array<string, string|null> $settings
     */
    public function updateSettings(int $rvid, array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = $this->findOneSetting($rvid, $key);

            if ($setting === null) {
                $setting = new JournalBackofficeSetting();
                $setting->setRvid($rvid);
                $setting->setSetting($key);
                $this->getEntityManager()->persist($setting);
            }

            $setting->setValue($value);
        }

        $this->getEntityManager()->flush();
    }
}