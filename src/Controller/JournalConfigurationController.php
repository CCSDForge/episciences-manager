<?php
namespace App\Controller;

use App\Service\JournalConfigurationService;
use App\Service\ReviewManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class JournalConfigurationController extends AbstractController
{
    #[Route('/journal/{code}/configuration', name: 'app_journal_configuration')]
    public function index(
        string $code,
        ReviewManager $reviewManager,
        JournalConfigurationService $configurationService
    ): Response {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            throw $this->createNotFoundException('Journal not found');
        }

        // Check if user has permission to view this specific review
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $configuration = $configurationService->getConfigurationArray($review['rvid']);

        return $this->render('journal_configuration/index.html.twig', [
            'review' => $review,
            'configuration' => $configuration,
            'rvcode' => $code,
        ]);
    }

    #[Route('/journal/{code}/configuration/show', name: 'app_journal_configuration_show', methods: ['GET'])]
    public function show(
        string $code,
        ReviewManager $reviewManager,
        JournalConfigurationService $configurationService
    ): JsonResponse {
        $review = $reviewManager->getReviewByCode($code);

        if ($review === null) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Journal not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $configuration = $configurationService->getConfigurationArray($review['rvid']);

        return new JsonResponse([
            'success' => true,
            'configuration' => $configuration,
        ]);
    }

    #[Route('/journal/{code}/configuration/edit', name: 'app_journal_configuration_update', methods: ['POST','PUT'])]
    public function update(
        string $code,
        Request $request,
        ReviewManager $reviewManager,
        JournalConfigurationService $configurationService
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

        $result = $configurationService->updateConfiguration($review['rvid'], $data);

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
            'message' => 'Configuration saved',
        ]);
    }


}

