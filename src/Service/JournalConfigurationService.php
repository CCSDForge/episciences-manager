<?php
namespace App\Service;

use App\Entity\JournalConfiguration;
use App\Repository\JournalConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class JournalConfigurationService
{
    public function __construct(
        private JournalConfigurationRepository $repository,
        private EntityManagerInterface         $entityManager,
        private JsonSchemaValidator            $schemaValidator
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
     * Validate configuration values using JSON Schema.
     *
     * @param array<string, mixed> $config
     * @return array<string, string> Validation errors
     */
    private function validateConfiguration(array $config): array
    {
        $result = $this->schemaValidator->validate($config, 'journal_configuration');

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