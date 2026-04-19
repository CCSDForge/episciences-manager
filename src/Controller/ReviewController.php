<?php

namespace App\Controller;


use App\Repository\PageRepository;
use App\Repository\ReviewRepository;
use App\Service\JournalSettingService;
use App\Service\ReviewManager;
use App\Service\PageHierarchyService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{

    #[Route('/journal', name: 'app_journal')]
    public function index(Request $request, ReviewManager $reviewManager, PaginatorInterface $paginator, ReviewRepository $reviewRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('You must be logged in');
        }

        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        // Count only reviews where user has a valid role
        $result = $reviewManager->getReviewsForUserPaginated($user, $paginator, $page, 30);

        $totalReviews = $reviewRepository->countAllReviews();
        $totalActiveReviews = $reviewRepository->countActiveReviews();

        if (!empty($search)) {
            $reviews = $reviewManager->searchReviewsForUser($user, $search);
            $pagination = null;
        } else {
            $reviews = $result['reviews'];
            $pagination = $result['pagination'];
        }

        return $this->render('review/journal.html.twig', [
            'reviews' => $reviews,
            'pagination' => $pagination,
            'search' => $search,
            'user' => $user,
            'current_page' => $page,
            'totalReviews' => $totalReviews,
            'totalActiveReviews' => $totalActiveReviews,
        ]);
    }


    #[Route('/journal/{code}', name: 'app_journal_detail', requirements: ['code' => '[\w\-]+'])]
    public function getJournal(string $code, ReviewManager $reviewManager, PageRepository $pageRepository, PageHierarchyService $hierarchyService, JournalSettingService $settingService): Response
    {
        // Get the review by its code
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        // Check if user has permission to view this specific review
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        return $this->render('review/journalDetails.html.twig', [
            'review' => $review,
            'code' => $code,
        ]);
    }

    #[Route('/journal/{code}/pages', name: 'app_journal_pages', requirements: ['code' => '[\w\-]+'])]
    public function pages(string $code, ReviewManager $reviewManager, PageRepository $pageRepository, PageHierarchyService $hierarchyService, JournalSettingService $settingService): Response
    {
        // Get the review by its code
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        // Check if user has permission to view this specific review
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        // Retrieve the journal pages
        $pages = $pageRepository->findBy([
            'rvcode' => $code
        ]);

        // Organize pages according to configured hierarchy
        $organizedPages = $hierarchyService->organizePages($pages, $code);

        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];

        $defaultLanguage = $setting['languages']['default'] ?? 'en';

        return $this->render('review/journalPages.html.twig', [
            'review' => $review,
            'code' => $code,
            'pages' => $organizedPages,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
        ]);
    }

    #[Route('/journal/{code}/pages/{pageCode}/edit', name: 'app_journal_page_edit', requirements: ['code' => '[\w\-]+', 'pageCode' => '[\w\-]+'])]
    public function pageEdit(string $code, string $pageCode, ReviewManager $reviewManager, PageRepository $pageRepository, PageHierarchyService $hierarchyService, JournalSettingService $settingService): Response
    {
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $pages = $pageRepository->findBy(['rvcode' => $code]);
        $organizedPages = $hierarchyService->organizePages($pages, $code);

        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];

        $defaultLanguage = $setting['languages']['default'] ?? 'en';

        return $this->render('review/journalPages.html.twig', [
            'review' => $review,
            'code' => $code,
            'pages' => $organizedPages,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
            'currentPage' => $pageCode,
            'editMode' => true,
        ]);
    }

    #[Route('/journal/{code}/pages/{pageCode}', name: 'app_journal_page_view', requirements: ['code' => '[\w\-]+', 'pageCode' => '[\w\-]+'])]
    public function pageView(string $code, string $pageCode, ReviewManager $reviewManager, PageRepository $pageRepository, PageHierarchyService $hierarchyService, JournalSettingService $settingService): Response
    {
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $pages = $pageRepository->findBy(['rvcode' => $code]);
        $organizedPages = $hierarchyService->organizePages($pages, $code);

        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];

        $defaultLanguage = $setting['languages']['default'] ?? 'en';

        return $this->render('review/journalPages.html.twig', [
            'review' => $review,
            'code' => $code,
            'pages' => $organizedPages,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
            'currentPage' => $pageCode,
            'editMode' => false,
        ]);
    }


}
