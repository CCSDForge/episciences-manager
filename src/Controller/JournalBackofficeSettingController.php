<?php

namespace App\Controller;

use App\Repository\JournalBackofficeSettingRepository;
use App\Service\ReviewManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class JournalBackofficeSettingController extends AbstractController
{
    /**
     * Display settings in read-only mode.
     * If no settings exist yet, redirect to edit mode for initial setup.
     */
    #[Route('/journal/{code}/backoffice-settings', name: 'app_journal_backoffice_settings', methods: ['GET'])]
    public function index(
        string $code,
        ReviewManager $reviewManager,
        JournalBackofficeSettingRepository $settingRepository
    ): Response {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            throw $this->createNotFoundException('Journal not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $settings = $settingRepository->getSettingArray($review['rvid']);

        // If no settings exist yet and user can edit, redirect to edit mode for initial setup
        $hasData = $this->hasAnySettingValue($settings);
        if (!$hasData && $this->isGranted('REVIEW_EDIT_BACKOFFICE_SETTINGS', $review)) {
            return $this->redirectToRoute('app_journal_backoffice_settings_edit', ['code' => $code]);
        }

        return $this->render('journalBackofficeSettings/index.html.twig', [
            'review' => $review,
            'settings' => $settings,
            'code' => $code,
            'canEditSettings' => $this->isGranted('REVIEW_EDIT_BACKOFFICE_SETTINGS', $review),
            'editMode' => false,
        ]);
    }

    /**
     * Check if any of the backoffice-specific settings has a non-empty value.
     */
    private function hasAnySettingValue(array $settings): bool
    {
        $backofficeKeys = ['journalDescription', 'journalKeywords', 'journalCreationYear'];

        foreach ($backofficeKeys as $key) {
            if (isset($settings[$key]) && $settings[$key] !== null && $settings[$key] !== '') {
                return true;
            }
        }
        return false;
    }

    /**
     * Display settings form for editing.
     */
    #[Route('/journal/{code}/backoffice-settings/edit', name: 'app_journal_backoffice_settings_edit', methods: ['GET'])]
    public function edit(
        string $code,
        ReviewManager $reviewManager,
        JournalBackofficeSettingRepository $settingRepository
    ): Response {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            throw $this->createNotFoundException('Journal not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_EDIT_BACKOFFICE_SETTINGS', $review);

        $settings = $settingRepository->getSettingArray($review['rvid']);

        return $this->render('journalBackofficeSettings/index.html.twig', [
            'review' => $review,
            'settings' => $settings,
            'code' => $code,
            'canEditSettings' => true,
            'editMode' => true,
        ]);
    }

    /**
     * Save settings and redirect to read-only view.
     */
    #[Route('/journal/{code}/backoffice-settings/edit', name: 'app_journal_backoffice_settings_update', methods: ['POST'])]
    public function update(
        string $code,
        Request $request,
        ReviewManager $reviewManager,
        JournalBackofficeSettingRepository $settingRepository
    ): Response {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            throw $this->createNotFoundException('Journal not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_EDIT_BACKOFFICE_SETTINGS', $review);

        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('journal-backoffice-settings', $token)) {
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('app_journal_backoffice_settings_edit', ['code' => $code]);
        }

        // Check if settings already exist (for flash message)
        $existingSettings = $settingRepository->getSettingArray($review['rvid']);
        $isCreation = !$this->hasAnySettingValue($existingSettings);

        $data = [
            'journalDescription' => $request->request->get('journalDescription'),
            'journalKeywords' => $request->request->get('journalKeywords'),
            'journalCreationYear' => $request->request->get('journalCreationYear'),
        ];

        $settingRepository->updateSettings($review['rvid'], $data);

        $flashMessage = $isCreation ? 'journalBackofficeSettings.created' : 'journalBackofficeSettings.updated';
        $this->addFlash('success', $flashMessage);

        return $this->redirectToRoute('app_journal_backoffice_settings', ['code' => $code]);
    }
}