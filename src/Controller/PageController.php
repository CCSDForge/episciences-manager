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
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    /**
     * Resolve a URL slug to the actual page code in database
     * Returns the actual page_code if an alias exists, otherwise returns the slug itself
     */
    public static function resolvePageCode(string $slug): string
    {
        return self::PAGE_ALIASES[$slug] ?? $slug;
    }

    #[Route('/journal/{code}/page/{pageTitle}', name: 'app_page_show', methods: ['GET'])]
    public function showPage(string $code, string $pageTitle, PageRepository $pageRepository, MarkdownService $markdownService, ReviewManager $reviewManager, PageHierarchyService $hierarchyService, JournalSettingService $settingService, EntityManagerInterface $entityManager, Request $request): Response
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
                // Get title from YAML config
                $yamlTitle = $hierarchyService->getTitleForPageCode($actualPageCode, $code);
                $title = $yamlTitle ?? array_fill_keys($acceptedLanguages, ucwords(str_replace('-', ' ', $actualPageCode)));
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

            // Get YAML title (source of truth for pages defined in YAML)
            $yamlTitle = $hierarchyService->getTitleForPageCode($actualPageCode, $code);
            $dbTitle = $page->getTitle();

            // If page is defined in YAML, sync YAML titles to DB if different
            if ($yamlTitle !== null) {
                $needsUpdate = false;
                foreach ($yamlTitle as $lang => $title) {
                    if (!isset($dbTitle[$lang]) || $dbTitle[$lang] !== $title) {
                        $needsUpdate = true;
                        break;
                    }
                }
                if ($needsUpdate) {
                    // Update DB with YAML titles
                    $page->setTitle($yamlTitle);
                    $entityManager->flush();
                    $dbTitle = $yamlTitle;
                }
            }

            // Use YAML title if available, otherwise DB title
            $mergedTitle = $yamlTitle ?? $dbTitle;

            // Ensure all accepted languages have at least a fallback title
            foreach ($acceptedLanguages as $lang) {
                if (empty($mergedTitle[$lang])) {
                    $mergedTitle[$lang] = $mergedTitle['en'] ?? ucwords(str_replace('-', ' ', $actualPageCode));
                }
            }

            return new JsonResponse([
                'title' => $mergedTitle,
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

        return $this->render('review/journalPages.html.twig', [
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
        ReviewManager $reviewManager,
        TranslatorInterface $translator
    ): Response {
        // Validate CSRF token (support both form and header)
        $token = $request->request->get('_token') ?? $request->headers->get('X-CSRF-Token');
        if (!$this->isCsrfTokenValid('page-edit', $token)) {
            $this->addFlash('danger', $translator->trans('journalPages.flash.invalidToken'));
            return $this->redirectToRoute('app_journal_page_view', [
                'code' => $code,
                'pageCode' => $pageTitle
            ]);
        }

        $review = $reviewManager->getReviewByCode($code);
        if (!$review) {
            throw $this->createNotFoundException('Journal not found');
        }
        if (!$this->isGranted('REVIEW_EDIT', $review)) {
            throw $this->createAccessDeniedException('Access denied');
        }

        $actualPageCode = self::PAGE_ALIASES[$pageTitle] ?? $pageTitle;

        $page = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $actualPageCode
        ]);

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

        // Get translations from form (all languages at once)
        $translations = $request->request->all('translations');

        if (empty($translations)) {
            $this->addFlash('warning', $translator->trans('journalPages.flash.noContent'));
            return $this->redirectToRoute('app_journal_page_view', [
                'code' => $code,
                'pageCode' => $pageTitle
            ]);
        }

        $currentTitle = $page->getTitle();
        $currentContent = $page->getContent();

        foreach ($translations as $lang => $data) {
            if (isset($data['title']) && !empty($data['title'])) {
                $currentTitle[$lang] = $data['title'];
            }
            if (isset($data['content'])) {
                $currentContent[$lang] = $data['content'];
            }
        }

        $page->setTitle($currentTitle);
        $page->setContent($currentContent);
        $page->setDateUpdated(new \DateTime());

        try {
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('journalPages.flash.saved'));
        } catch (\Throwable $e) {
            $this->logger->error('Error saving page', [
                'exception' => $e->getMessage(),
                'code' => $code,
                'pageTitle' => $pageTitle,
            ]);
            $this->addFlash('danger', $translator->trans('journalPages.flash.error'));
        }

        return $this->redirectToRoute('app_journal_page_view', [
            'code' => $code,
            'pageCode' => $pageTitle
        ]);
    }
}
