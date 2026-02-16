<?php

namespace App\Controller;


use App\Repository\PageRepository;
use App\Service\MarkdownService;
use App\Service\ReviewManager;
use App\Service\PageHierarchyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PageController extends AbstractController
{
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
    public function showPage(string $code, string $pageTitle, PageRepository $pageRepository, MarkdownService $markdownService, ReviewManager $reviewManager, PageHierarchyService $hierarchyService, Request $request): Response
    {
        // Check if there's an alias for this pageTitle
        $actualPageCode = self::PAGE_ALIASES[$pageTitle] ?? $pageTitle;

        // Find the page using the actual page code
        $page = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $actualPageCode
        ]);

        // If it's an AJAX request, return JSON
        if ($request->isXmlHttpRequest()) {
            // If page doesn't exist, return empty content
            if (!$page) {
                return new JsonResponse([
                    'title' => ['en' => ucwords(str_replace('-', ' ', $actualPageCode)), 'fr' => ucwords(str_replace('-', ' ', $actualPageCode))],
                    'content' => ['en' => '', 'fr' => ''],
                    'pageCode' => $actualPageCode,
                    'isEmpty' => true
                ]);
            }

            // Convert markdown content to HTML
            $htmlContent = $markdownService->convertContentArray($page->getContent());

            return new JsonResponse([
                'title' => $page->getTitle(),
                'content' => $htmlContent,
                'pageCode' => $page->getPageCode()
            ]);
        }

        if (!$page) {
            throw $this->createNotFoundException('Page not found');
        }

        // For direct access, render the journal page with the current page preselected
        // Get review and organize pages like in ReviewController
        $review = $reviewManager->getReviewByCode($code);
        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        // Check permissions
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        // Get all pages for the journal
        $allPages = $pageRepository->findBy(['rvcode' => $code]);
        $organizedPages = $hierarchyService->organizePages($allPages, $code);

        return $this->render('review/journalDetails.html.twig', [
            'review' => $review,
            'code' => $code,
            'pages' => $organizedPages,
            'currentPageCode' => $pageTitle, // Pass the current page code to the template
        ]);
    }

    #[Route('/journal/{code}/page/{pageTitle}/edit', name: 'app_page_edit', methods: ['POST'])]
    public function editPage(
        string $code,
        string $pageTitle,
        Request $request,
        PageRepository $pageRepository,
        EntityManagerInterface $entityManager,
        MarkdownService $markdownService
    ): JsonResponse {
        // Check if there's an alias for this pageTitle
        $actualPageCode = self::PAGE_ALIASES[$pageTitle] ?? $pageTitle;

        $page = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $actualPageCode
        ]);

        $data = json_decode($request->getContent(), true);

        // If page doesn't exist, create it
        if (!$page) {
            $page = new \App\Entity\Page();
            $page->setRvcode($code);
            $page->setPageCode(strtolower($actualPageCode));
            $page->setUid($this->getUser()?->getUid() ?? 0);
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

        $htmlContent = (string) $data['content']; // CKEditor HTML
        $locale      = (string) $data['locale'];
        $title       = isset($data['title']) ? (string) $data['title'] : null;

        // Convert HTML -> Markdown
        try {
            $markdownContent = $markdownService->toMarkdown($htmlContent);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error converting content: ' . $e->getMessage()
            ], 500);
        }

        // Save markdown per locale
        $currentContent = $page->getContent() ?? [];
        $currentContent[$locale] = $markdownContent;
        $page->setContent($currentContent);

        if ($title !== null) {
            $currentTitle = $page->getTitle() ?? [];
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
            return new JsonResponse([
                'success' => false,
                'message' => 'Error saving page: ' . $e->getMessage()
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
            'welcome' => $translator->trans('head.welcome', [], 'messages', $locale),
            'login' => $translator->trans('head.login', [], 'messages', $locale),
            'logout' => $translator->trans('head.logout', [], 'messages', $locale)
        ];

        return new JsonResponse($translations);
    }

}