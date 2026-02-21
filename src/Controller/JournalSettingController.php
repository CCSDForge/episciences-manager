<?php
namespace App\Controller;

use App\Service\JournalSettingService;
use App\Service\ReviewManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

class JournalSettingController extends AbstractController
{
    #[Route('/journal/{code}/settings', name: 'app_journal_settings')]
    public function index(
        string $code,
        ReviewManager $reviewManager,
        JournalSettingService $settingService
    ): Response {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            throw $this->createNotFoundException('Journal not found');
        }

        // Check if user has permission to view this specific review
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $setting = $settingService->getSettingArray($review['rvid']);

        return $this->render('journal_settings/index.html.twig', [
            'review' => $review,
            'setting' => $setting,
            'code' => $code,
            'homepageOptions' => $settingService->getHomepageOptions(),
            'menuOptions' => $settingService->getMenuOptions(),
            'statsOptions' => $settingService->getStatisticsOptions(),
        ]);
    }

    #[Route('/journal/{code}/settings/show', name: 'app_journal_settings_show', methods: ['GET'])]
    public function show(
        string $code,
        ReviewManager $reviewManager,
        JournalSettingService $settingService
    ): JsonResponse {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Journal not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $setting = $settingService->getSettingArray($review['rvid']);

        return new JsonResponse([
            'success' => true,
            'setting' => $setting,
        ]);
    }

    #[Route('/journal/{code}/settings/edit', name: 'app_journal_settings_update', methods: ['POST','PUT'])]
    #[IsCsrfTokenValid('journal-settings')]
    public function update(
        string $code,
        Request $request,
        ReviewManager $reviewManager,
        JournalSettingService $settingService
    ): JsonResponse {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Journal not found'],
                Response::HTTP_NOT_FOUND
            );
        }

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

