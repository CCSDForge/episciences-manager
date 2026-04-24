<?php
namespace App\Controller;

use App\Service\JournalSettingService;
use App\Service\ReviewManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $setting = $settingService->getSettingArray($review['rvid']);

        return $this->render('journal_settings/index.html.twig', [
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
        JournalSettingService $settingService
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
        JournalSettingService $settingService,
        LoggerInterface $logger,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse {
        // Validate CSRF token from query parameter
        $token = $request->query->get('_token');
        $expectedToken = $csrfTokenManager->getToken('journal-settings')->getValue();
        file_put_contents('/tmp/csrf_debug.log', date('Y-m-d H:i:s') . "\n  Token reçu:   " . var_export($token, true) . "\n  Token attendu: " . $expectedToken . "\n  Valide: " . ($this->isCsrfTokenValid('journal-settings', $token) ? 'OUI' : 'NON') . "\n\n", FILE_APPEND);
        $logger->info('CSRF Token validation', [
            'token_received' => $token,
            'token_id' => 'journal-settings',
            'is_valid' => $this->isCsrfTokenValid('journal-settings', $token),
        ]);
        if (!$this->isCsrfTokenValid('journal-settings', $token)) {
            $logger->warning('Invalid CSRF token', ['token' => $token]);
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

