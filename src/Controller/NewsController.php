<?php

namespace App\Controller;


use App\Repository\NewsRepository;
use App\Repository\PageRepository;
use App\Service\JournalSettingService;
use App\Service\MarkdownService;
use App\Service\ReviewManager;
use App\Service\PageHierarchyService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NewsController extends AbstractController
{
    #[Route('/journal/{code}/news', name: 'app_news_show', methods: ['GET'])]
    public function index(string $code, ReviewManager $reviewManager,NewsRepository $newsRepository,Security $security, JournalSettingService $settingService): Response
    {
        // Récupérer l'utilisateur connecté (via CAS)
        $user = $security->getUser();
        $uid = $user?->getUid();

        // Get the review by its code
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        // Check if user has permission to view this specific review
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $newsList = $newsRepository->findByRvcode($code);

        // Get accepted languages for this journal
        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];

        $defaultLanguage = $setting['languages']['default'] ?? 'en';

        return $this->render('news/journalNews.html.twig', [
            'review' => $review,
            'code' => $code,
            'newsList' => $newsList,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
            'currentUser' => $user,
        ]);
    }

    #[Route('/journal/{code}/news/{id}/edit', name: 'app_news_edit', methods: ['GET', 'POST'])]
    public function edit(
        string $code,
        int $id,
        ReviewManager $reviewManager,
        NewsRepository $newsRepository,
        Security $security,
        Request $request,
        JournalSettingService $settingService
    ): Response
    {
        // Récupérer le journal
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        // Récupérer la news
        $news = $newsRepository->find($id);

        if (!$news || $news->getRvcode() !== $code) {
            throw $this->createNotFoundException('News not found');
        }

        // Get accepted languages for this journal
        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];

        $defaultLanguage = $setting['languages']['default'] ?? 'en';

        return $this->render('news/editNews.html.twig', [
            'review' => $review,
            'code' => $code,
            'news' => $news,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
        ]);
    }

}
