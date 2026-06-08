<?php
namespace App\Controller;

use App\Service\JournalFrontendSettingService;
use App\Service\ReviewManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class JournalFrontendSettingController extends AbstractController
{
    #[Route('/journal/{code}/settings', name: 'app_journal_settings')]
    public function index(
        string $code,
        ReviewManager $reviewManager,
        JournalFrontendSettingService $settingService
    ): Response {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            throw $this->createNotFoundException('Journal not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $setting = $settingService->getSettingArray($review['rvid']);

        return $this->render('journalFrontendSettings/index.html.twig', [
            'review' => $review,
            'setting' => $setting,
            'code' => $code,
            'homepageOptions' => $settingService->getHomepageOptions(),
            'menuOptions' => $settingService->getMenuOptions(),
            'statsOptions' => $settingService->getStatisticsOptions(),
            'canEditSettings' => $this->isGranted('REVIEW_EDIT_SETTINGS', $review),
        ]);
    }

    #[Route('/journal/{code}/settings/show', name: 'app_journal_settings_show', methods: ['GET'])]
    public function show(
        string $code,
        ReviewManager $reviewManager,
        JournalFrontendSettingService $settingService
    ): JsonResponse {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Journal not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        if (!$this->isGranted('REVIEW_VIEW', $review)) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Access denied'],
                Response::HTTP_FORBIDDEN
            );
        }

        $setting = $settingService->getSettingArray($review['rvid']);

        return new JsonResponse([
            'success' => true,
            'setting' => $setting,
        ]);
    }

    #[Route('/journal/{code}/settings/edit', name: 'app_journal_settings_update', methods: ['POST','PUT'])]
    public function update(
        string $code,
        Request $request,
        ReviewManager $reviewManager,
        JournalFrontendSettingService $settingService
    ): JsonResponse {
        // Validate CSRF token from query parameter
        $token = $request->query->get('_token');
        if (!$this->isCsrfTokenValid('journal-settings', $token)) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Invalid CSRF token'],
                Response::HTTP_FORBIDDEN
            );
        }

        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Journal not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->denyAccessUnlessGranted('REVIEW_EDIT_SETTINGS', $review);

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $result = $settingService->updateSetting($review['rvid'], $data);

        if ($result['success'] === false) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $result['errors'],
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Settings saved',
        ]);
    }


}

