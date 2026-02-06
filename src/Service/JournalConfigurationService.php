<?php
namespace App\Service;

use App\Entity\JournalConfiguration;
use App\Repository\JournalConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class JournalConfigurationService
{
    public function __construct(
        private JournalConfigurationRepository $repository,
        private EntityManagerInterface         $entityManager
    )
    {
    }

    public function getDefaultConfiguration(): array
    {
        return [
            'api_domain' => '',
            'theme' => [
                'primaryColor' => '#49737e',
                'primaryTextColor' => '#ffffff',
            ],
            'languages' => [
                'accepted' => ['en', 'fr','es'],
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
                'lastInformationRenderType' => 'last-news',
            ],
            'menu' => [
                'acceptedArticlesRender' => true,
                'volumeTypeProceedingsRender' => true,
                'specialIssuesRender' => true,
                'sectionsRender' => true,
                'newsRender' => true,
                'journalIndexingRender' => true,
                'journalAcknowledgementsRender' => true,
                'journalForReviewersRender' => true,
                'journalForConferenceOrganisersRender' => true,
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
     * Get a configuration entity by its RVID.
     *
     * @param int $rvid
     * @return JournalConfiguration|null
     */
    public function getByRvid(int $rvid): ?JournalConfiguration
    {
        return $this->repository->findByRvid($rvid);
    }

    /**
     * Get the configuration for the given RVID or create it with default values.
     *
     * @param int $rvid
     * @return JournalConfiguration
     */
    public function getOrCreateConfiguration(int $rvid): JournalConfiguration
    {
        $configuration = $this->repository->findByRvid($rvid);

        if ($configuration === null) {
            $configuration = new JournalConfiguration();
            $configuration->setRvid($rvid);
            $configuration->setConfiguration($this->getDefaultConfiguration());
            $configuration->setCreatedAt(new \DateTime());
            $configuration->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($configuration);
            $this->entityManager->flush();
        }

        return $configuration;
    }

    /**
     * Get the configuration as an array merged with default values.
     *
     * @param int $rvid
     * @return array<string, mixed>
     */
    public function getConfigurationArray(int $rvid): array
    {
        $configuration = $this->getOrCreateConfiguration($rvid);

        return $this->mergeWithDefaults($configuration->getConfiguration());
    }

    /**
     * Update the configuration for the given RVID.
     *
     * @param int $rvid
     * @param array<string, mixed> $newConfiguration
     *
     * @return array{
     *     success: bool,
     *     errors?: array<string, string>,
     *     configuration?: JournalConfiguration
     * }
     */
    public function updateConfiguration(int $rvid, array $newConfiguration): array
    {
        $errors = $this->validateConfiguration($newConfiguration);

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        $configuration = $this->getOrCreateConfiguration($rvid);

        $mergedConfiguration = $this->mergeWithDefaults($newConfiguration);

        $configuration->setConfiguration($mergedConfiguration);
        $configuration->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return [
            'success' => true,
            'configuration' => $configuration,
        ];
    }

    /**
     * Validate configuration values.
     *
     * @param array<string, mixed> $config
     * @return array<string, string> Validation errors indexed by field path
     */
    private function validateConfiguration(array $config): array
    {
        $errors = [];

        if (
            isset($config['api_domain']) &&
            is_string($config['api_domain']) &&
            !filter_var($config['api_domain'], FILTER_VALIDATE_URL)
        ) {
            $errors['api_domain'] = 'Invalid URL format';
        }

        if (isset($config['theme']) && is_array($config['theme'])) {
            if (
                isset($config['theme']['primaryColor']) &&
                is_string($config['theme']['primaryColor']) &&
                !$this->isValidHexColor($config['theme']['primaryColor'])
            ) {
                $errors['theme.primaryColor'] = 'Invalid hex color format';
            }

            if (
                isset($config['theme']['primaryTextColor']) &&
                is_string($config['theme']['primaryTextColor']) &&
                !$this->isValidHexColor($config['theme']['primaryTextColor'])
            ) {
                $errors['theme.primaryTextColor'] = 'Invalid hex color format';
            }
        }

        $validLanguages = ['en', 'fr', 'es'];

        if (isset($config['languages']) && is_array($config['languages'])) {
            if (isset($config['languages']['accepted']) && is_array($config['languages']['accepted'])) {
                foreach ($config['languages']['accepted'] as $lang) {
                    if (!is_string($lang) || !in_array($lang, $validLanguages, true)) {
                        $errors['languages.accepted'] = "Invalid language: {$lang}";
                        break;
                    }
                }
            }

            if (
                isset($config['languages']['default']) &&
                is_string($config['languages']['default']) &&
                !in_array($config['languages']['default'], $validLanguages, true)
            ) {
                $errors['languages.default'] = 'Invalid default language';
            }
        }

        if (
            isset($config['statistics']['colors']) &&
            is_array($config['statistics']['colors'])
        ) {
            foreach ($config['statistics']['colors'] as $index => $color) {
                if (!is_string($color) || !$this->isValidHexColor($color)) {
                    $errors["statistics.colors.$index"] = 'Invalid hex color format';
                }
            }
        }

        return $errors;
    }

    /**
     * Determine whether a value is a valid hex color.
     *
     * @param string $color
     * @return bool
     */
    private function isValidHexColor(string $color): bool
    {
        return preg_match('/^#[0-9A-Fa-f]{6}$/', $color) === 1;
    }

    /**
     * Merge a configuration array with default configuration values.
     *
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function mergeWithDefaults(array $config): array
    {
        return array_replace_recursive($this->getDefaultConfiguration(), $config);
    }

}