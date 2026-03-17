<?php
namespace App\Service;

use App\Entity\JournalSetting;
use App\Repository\JournalSettingRepository;
use Doctrine\ORM\EntityManagerInterface;

class JournalSettingService
{
    public function __construct(
        private JournalSettingRepository $repository,
        private EntityManagerInterface   $entityManager,
        private JsonSchemaValidator      $schemaValidator
    )
    {
    }

    /**
     * Get available homepage options.
     *
     * @return array<string>
     */
    public function getHomepageOptions(): array
    {
        return [
            'latestArticlesCarouselRender',
            'latestNewsCarouselRender',
            'membersCarouselRender',
            'statsRender',
            'journalIndexationRender',
            'specialIssuesRender',
            'latestAcceptedArticlesCarouselRender',
            'lastNewsRender',
        ];
    }

    /**
     * Get available menu options.
     *
     * @return array<string>
     */
    public function getMenuOptions(): array
    {
        return [
            'acceptedArticlesRender',
            'volumeTypeProceedingsRender',
            'specialIssuesRender',
            'sectionsRender',
            'authorsRender',
            'journalIndexingRender',
            'journalAcknowledgementsRender',
            'journalEthicalCharterRender',
            'journalForReviewersRender',
            'journalForConferenceOrganisersRender',
        ];
    }

    /**
     * Get available statistics options.
     *
     * @return array<string>
     */
    public function getStatisticsOptions(): array
    {
        return [
            'acceptanceRate',
            'nbSubmissions',
            'nbSubmissionsDetails',
            'reviewsRequested',
            'reviewsReceived',
            'medianSubmissionPublication',
            'medianReviewsNumber',
        ];
    }

    public function getDefaultSetting(): array
    {
        return [
            'api_domain' => '',
            'theme' => [
                'primaryColor' => '#49737e',
                'primaryTextColor' => '#ffffff',
            ],
            'languages' => [
                'accepted' => ['en', 'fr', 'es'],
                'default' => 'en',
            ],
            'homepage' => [
                'latestArticlesCarouselRender' => true,
                'latestNewsCarouselRender' => true,
                'membersCarouselRender' => true,
                'statsRender' => true,
                'journalIndexationRender' => true,
                'specialIssuesRender' => true,
                'latestAcceptedArticlesCarouselRender' => true,
                'lastNewsRender' => true,
            ],
            'homepageRightBlock' => [
                'lastInformationRenderType' => 'last-news',
            ],
            'menu' => [
                'acceptedArticlesRender' => true,
                'volumeTypeProceedingsRender' => true,
                'specialIssuesRender' => true,
                'sectionsRender' => true,
                'authorsRender' => true,
                'journalIndexingRender' => false,
                'journalAcknowledgementsRender' => false,
                'journalEthicalCharterRender' => true,
                'journalForReviewersRender' => false,
                'journalForConferenceOrganisersRender' => false,
            ],
            'statistics' => [
                'colors' => ['#840909', '#295fba', '#3f557a', '#192132'],
                'acceptanceRate' => ['render' => true, 'order' => 10],
                'nbSubmissions' => ['render' => true, 'order' => 2],
                'nbSubmissionsDetails' => ['render' => true, 'order' => 3],
                'reviewsRequested' => ['render' => true, 'order' => 1],
                'reviewsReceived' => ['render' => true, 'order' => 2],
                'medianSubmissionPublication' => ['render' => true, 'order' => 3],
                'medianReviewsNumber' => ['render' => true, 'order' => 4],
            ],
        ];
    }

    /**
     * Get a setting entity by its RVID.
     */
    public function getByRvid(int $rvid): ?JournalSetting
    {
        return $this->repository->findByRvid($rvid);
    }

    /**
     * Get the setting for the given RVID or create it with default values.
     */
    public function getOrCreateSetting(int $rvid): JournalSetting
    {
        $setting = $this->repository->findByRvid($rvid);

        if (!$setting instanceof \App\Entity\JournalSetting) {
            $setting = new JournalSetting();
            $setting->setRvid($rvid);
            $setting->setSetting($this->getDefaultSetting());
            $setting->setCreatedAt(new \DateTime());
            $setting->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($setting);
            $this->entityManager->flush();
        }

        return $setting;
    }

    /**
     * Get the setting as an array merged with default values.
     *
     * @return array<string, mixed>
     */
    public function getSettingArray(int $rvid): array
    {
        $setting = $this->getOrCreateSetting($rvid);

        return $this->mergeWithDefaults($setting->getSetting());
    }

    /**
     * Update the setting for the given RVID.
     *
     * @param array<string, mixed> $newSetting
     * @return array{
     *     success: bool,
     *     errors?: array<string, string>,
     *     setting?: JournalSetting
     * }
     */
    public function updateSetting(int $rvid, array $newSetting): array
    {

        $errors = $this->validateSetting($newSetting);

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        $setting = $this->getOrCreateSetting($rvid);

        // Merge new settings with existing and default settings
        $existingSetting = $setting->getSetting();
        $mergedSetting = array_replace_recursive($this->getDefaultSetting(), $existingSetting, $newSetting);

        // For indexed arrays like languages.accepted, use new value directly instead of merging
        if (isset($newSetting['languages']['accepted'])) {
            $mergedSetting['languages']['accepted'] = $newSetting['languages']['accepted'];
        }

        // Clone array to force Doctrine to detect the change (JSON columns comparison issue)
        $setting->setSetting(json_decode(json_encode($mergedSetting), true));
        $setting->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return [
            'success' => true,
            'setting' => $setting,
        ];
    }

    /**
     * Validate setting values using JSON Schema.
     *
     * @param array<string, mixed> $config
     * @return array<string, string> Validation errors
     */
    private function validateSetting(array $config): array
    {
        $result = $this->schemaValidator->validate($config, 'journal_setting');

        if ($result['valid']) {
            return [];
        }

        // Convert errors array to associative array format
        $errors = [];
        foreach ($result['errors'] as $index => $error) {
            $errors["validation_error_{$index}"] = $error;
        }

        return $errors;
    }

    /**
     * Merge a setting array with default setting values.
     *
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function mergeWithDefaults(array $config): array
    {
        $merged = array_replace_recursive($this->getDefaultSetting(), $config);

        // For indexed arrays like languages.accepted, use config value directly instead of merging
        if (isset($config['languages']['accepted'])) {
            $merged['languages']['accepted'] = $config['languages']['accepted'];
        }

        return $merged;
    }

}