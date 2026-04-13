<?php

namespace App\Controller;


use App\Repository\PageRepository;
use App\Service\JournalSettingService;
use App\Service\MarkdownService;
use App\Service\ReviewManager;
use App\Service\PageHierarchyService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PageController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }
    // URL aliases mapping: URL slug => actual page_code in database
    private const PAGE_ALIASES = [
        'acknowledgements' => 'journal-acknowledgements',
        'indexing' => 'journal-indexing',
    ];

    /**
     * Get the URL slug for a given page code
     * Returns the short alias if one exists, otherwise returns the page code itself
     */
    public static function getPageSlug(string $pageCode): string
    {
        // Reverse lookup: find the alias key for this page code
        $alias = array_search($pageCode, self::PAGE_ALIASES, true);
        return $alias !== false ? $alias : $pageCode;
    }

    #[Route('/journal/{code}/page/{pageTitle}', name: 'app_page_show', methods: ['GET'])]
    public function showPage(string $code, string $pageTitle, PageRepository $pageRepository, MarkdownService $markdownService, ReviewManager $reviewManager, PageHierarchyService $hierarchyService, JournalSettingService $settingService, Request $request): Response
    {
        // Check permissions first
        $review = $reviewManager->getReviewByCode($code);
        if (!$review) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => 'Journal not found'], Response::HTTP_NOT_FOUND);
            }
            throw $this->createNotFoundException('Review not found');
        }

        if (!$this->isGranted('REVIEW_VIEW', $review)) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
            }
            throw $this->createAccessDeniedException('Access denied');
        }

        // Check if there's an alias for this pageTitle
        $actualPageCode = self::PAGE_ALIASES[$pageTitle] ?? $pageTitle;

        // Find the page using the actual page code
        $page = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $actualPageCode
        ]);

        // Get accepted languages for this journal
        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];

        // If it's an AJAX request, return JSON
        if ($request->isXmlHttpRequest()) {
            // If page doesn't exist, return empty content
            if (!$page instanceof \App\Entity\Page) {
                $defaultTitle = ucwords(str_replace('-', ' ', $actualPageCode));
                $title = array_fill_keys($acceptedLanguages, $defaultTitle);
                $emptyContent = array_fill_keys($acceptedLanguages, '');

                return new JsonResponse([
                    'title' => $title,
                    'content' => $emptyContent,
                    'markdownContent' => $emptyContent,
                    'pageCode' => $actualPageCode,
                    'isEmpty' => true
                ]);
            }

            // Convert markdown content to HTML
            $htmlContent = $markdownService->convertContentArray($page->getContent());

            return new JsonResponse([
                'title' => $page->getTitle(),
                'content' => $htmlContent,
                'markdownContent' => $page->getContent(),
                'pageCode' => $page->getPageCode()
            ]);
        }

        if (!$page instanceof \App\Entity\Page) {
            throw $this->createNotFoundException('Page not found');
        }

        // Get all pages for the journal
        $allPages = $pageRepository->findBy(['rvcode' => $code]);
        $organizedPages = $hierarchyService->organizePages($allPages, $code);

        return $this->render('review/journalDetails.html.twig', [
            'review' => $review,
            'code' => $code,
            'pages' => $organizedPages,
            'currentPageCode' => $pageTitle,
            'acceptedLanguages' => $acceptedLanguages,
        ]);
    }

    #[Route('/journal/{code}/page/{pageTitle}/edit', name: 'app_page_edit', methods: ['POST'])]
    public function editPage(
        string $code,
        string $pageTitle,
        Request $request,
        PageRepository $pageRepository,
        EntityManagerInterface $entityManager,
        MarkdownService $markdownService,
        ReviewManager $reviewManager
    ): JsonResponse {
        // Validate CSRF token
        $token = $request->headers->get('X-CSRF-Token') ?? $request->query->get('_token');
        if (!$this->isCsrfTokenValid('page-edit', $token)) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Invalid CSRF token'],
                Response::HTTP_FORBIDDEN
            );
        }

        $review = $reviewManager->getReviewByCode($code);
        if (!$review) {
            return new JsonResponse(['success' => false, 'message' => 'Journal not found'], 404);
        }
        if (!$this->isGranted('REVIEW_EDIT', $review)) {
            return new JsonResponse(
                ['success' => false, 'message' => 'Access denied'],
                Response::HTTP_FORBIDDEN
            );
        }

        $actualPageCode = self::PAGE_ALIASES[$pageTitle] ?? $pageTitle;

        $page = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $actualPageCode
        ]);

        $data = json_decode($request->getContent(), true);

        // If page doesn't exist, create it
        if (!$page instanceof \App\Entity\Page) {
            $page = new \App\Entity\Page();
            $page->setRvcode($code);
            $page->setPageCode(strtolower($actualPageCode));
            /** @var \App\Entity\User|null $user */
            $user = $this->getUser();
            $page->setUid($user?->getUid() ?? 0);
            $page->setTitle([]);
            $page->setContent([]);
            $page->setVisibility(['public']);
            $page->setDateCreation(new \DateTime());
            $page->setDateUpdated(new \DateTime());
            $entityManager->persist($page);
        }

        if (!isset($data['content'], $data['locale'])) {
            return new JsonResponse(['success' => false, 'message' => 'Missing content or locale'], 400);
        }

        $markdownContent = (string) $data['content'];
        $locale      = (string) $data['locale'];
        $title       = isset($data['title']) ? (string) $data['title'] : null;

        // Save markdown per locale
        $currentContent = $page->getContent();
        $currentContent[$locale] = $markdownContent;
        $page->setContent($currentContent);

        if ($title !== null) {
            $currentTitle = $page->getTitle();
            $currentTitle[$locale] = $title;
            $page->setTitle($currentTitle);
        }

        // Always update the date_updated timestamp
        $page->setDateUpdated(new \DateTime());

        try {
            $entityManager->flush();

            // Return fresh HTML for the locale (MD -> HTML)
            $htmlByLocale = $markdownService->convertContentArray($page->getContent());
            $htmlForLocale = $htmlByLocale[$locale] ?? '';

            return new JsonResponse([
                'success'      => true,
                'message'      => 'Page updated successfully',
                'htmlContent'  => $htmlForLocale,
                'updatedTitle' => $title !== null ? ($page->getTitle()[$locale] ?? '') : null
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Error saving page', [
                'exception' => $e->getMessage(),
                'code' => $code,
                'pageTitle' => $pageTitle,
            ]);
            return new JsonResponse([
                'success' => false,
                'message' => 'Error saving page'
            ], 500);
        }
    }


    #[Route('/api/translations/{locale}', name: 'app_translations', methods: ['GET'])]
    public function getTranslations(string $locale, TranslatorInterface $translator, Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException('This endpoint only accepts AJAX requests');
        }

        // Set the locale for the translator
        $request->setLocale($locale);

        $translations = [
            'selectPageFirst' => $translator->trans('journalDetails.select_page_first', [], 'messages', $locale),
            'missingPageInfo' => $translator->trans('journalDetails.missing_page_info', [], 'messages', $locale),
            'saveSuccess' => $translator->trans('journalDetails.save_success', [], 'messages', $locale),
            'saveError' => $translator->trans('journalDetails.save_error', [], 'messages', $locale),
            'edit' => $translator->trans('journalDetails.edit', [], 'messages', $locale),
            'editPageContent' => $translator->trans('journalDetails.edit_page_content', [], 'messages', $locale),
            'pageTitle' => $translator->trans('journalDetails.page_title', [], 'messages', $locale),
            'content' => $translator->trans('journalDetails.content', [], 'messages', $locale),
            'enterContent' => $translator->trans('journalDetails.enter_content', [], 'messages', $locale),
            'cancel' => $translator->trans('journalDetails.cancel', [], 'messages', $locale),
            'save' => $translator->trans('journalDetails.save', [], 'messages', $locale),
            'welcomeBackoffice' => $translator->trans('journalDetails.welcome_backoffice', [], 'messages', $locale),
            'previewPage' => $translator->trans('journalDetails.preview_page', [], 'messages', $locale),
            'noContentAvailable' => $translator->trans('journalDetails.noContentAvailable', [], 'messages', $locale),
            'welcome' => $translator->trans('head.welcome', [], 'messages', $locale),
            'login' => $translator->trans('head.login', [], 'messages', $locale),
            'logout' => $translator->trans('head.logout', [], 'messages', $locale)
        ];

        return new JsonResponse($translations);
    }

}
