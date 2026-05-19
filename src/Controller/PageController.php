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

        return $this->render('pages/journalPages.html.twig', [
            'review' => $review,
            'code' => $code,
            'pages' => $organizedPages,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
        ]);
    }

    #[Route('/journal/{code}/pages/{pageCode}/edit', name: 'app_journal_page_edit', requirements: ['code' => '[\w\-]+', 'pageCode' => '[\w\-]+'], methods: ['GET', 'POST'])]
    public function pageEdit(
        string $code,
        string $pageCode,
        Request $request,
        ReviewManager $reviewManager,
        PageRepository $pageRepository,
        PageHierarchyService $hierarchyService,
        JournalSettingService $settingService,
        MarkdownService $markdownService,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): Response {
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_EDIT', $review);

        // Resolve page alias if needed
        $actualPageCode = self::resolvePageCode($pageCode);

        // Handle POST request (save changes)
        if ($request->isMethod('POST')) {
            // Validate CSRF token
            $token = $request->request->get('_token') ?? $request->headers->get('X-CSRF-Token');
            if (!$this->isCsrfTokenValid('page-edit', $token)) {
                $this->addFlash('danger', $translator->trans('journalPages.flash.invalidToken'));
                return $this->redirectToRoute('app_journal_page_view', [
                    'code' => $code,
                    'pageCode' => $pageCode
                ]);
            }

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

            // Get translations from form
            $translations = $request->request->all('translations');

            if (empty($translations)) {
                $this->addFlash('warning', $translator->trans('journalPages.flash.noContent'));
                return $this->redirectToRoute('app_journal_page_view', [
                    'code' => $code,
                    'pageCode' => $pageCode
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

                // Store the edited language to display content in that language after redirect
                $editedLanguage = $request->request->get('language');
                if ($editedLanguage) {
                    $request->getSession()->set('content_language', $editedLanguage);
                }
            } catch (\Throwable $e) {
                $this->logger->error('Error saving page', [
                    'exception' => $e->getMessage(),
                    'code' => $code,
                    'pageCode' => $pageCode,
                ]);
                $this->addFlash('danger', $translator->trans('journalPages.flash.error'));
            }

            return $this->redirectToRoute('app_journal_page_view', [
                'code' => $code,
                'pageCode' => $pageCode
            ]);
        }

        // Handle GET request (show edit form)
        $pages = $pageRepository->findBy(['rvcode' => $code]);
        $organizedPages = $hierarchyService->organizePages($pages, $code);

        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];
        $defaultLanguage = $setting['languages']['default'] ?? 'en';

        // Fetch the current page data
        $currentPage = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $actualPageCode
        ]);

        // Get YAML title (source of truth for pages defined in YAML)
        $yamlTitle = $hierarchyService->getTitleForPageCode($actualPageCode, $code);

        // Build page data for template
        $currentPageData = null;
        if ($currentPage) {
            $htmlContent = $markdownService->convertContentArray($currentPage->getContent());
            $title = $yamlTitle ?? $currentPage->getTitle();

            $currentPageData = [
                'title' => $title,
                'content' => $htmlContent,
                'markdownContent' => $currentPage->getContent(),
                'pageCode' => $currentPage->getPageCode(),
            ];
        } elseif ($yamlTitle) {
            // Page defined in YAML but no DB entry yet
            $currentPageData = [
                'title' => $yamlTitle,
                'content' => array_fill_keys($acceptedLanguages, ''),
                'markdownContent' => array_fill_keys($acceptedLanguages, ''),
                'pageCode' => $actualPageCode,
            ];
        }

        // Get breadcrumb path
        $breadcrumbPath = $hierarchyService->getBreadcrumbPath($actualPageCode, $code);

        return $this->render('pages/journalPages.html.twig', [
            'review' => $review,
            'code' => $code,
            'pages' => $organizedPages,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
            'currentPage' => $pageCode,
            'currentPageData' => $currentPageData,
            'breadcrumbPath' => $breadcrumbPath,
            'editMode' => true,
        ]);
    }

    #[Route('/journal/{code}/pages/{pageCode}', name: 'app_journal_page_view', requirements: ['code' => '[\w\-]+', 'pageCode' => '[\w\-]+'])]
    public function pageView(string $code, string $pageCode, Request $request, ReviewManager $reviewManager, PageRepository $pageRepository, PageHierarchyService $hierarchyService, JournalSettingService $settingService, MarkdownService $markdownService): Response
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

        // Resolve page alias if needed
        $actualPageCode = self::resolvePageCode($pageCode);

        // Fetch the current page data
        $currentPage = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $actualPageCode
        ]);

        // Get YAML title (source of truth for pages defined in YAML)
        $yamlTitle = $hierarchyService->getTitleForPageCode($actualPageCode, $code);

        // Build page data for template
        $currentPageData = null;
        if ($currentPage) {
            $htmlContent = $markdownService->convertContentArray($currentPage->getContent());
            $title = $yamlTitle ?? $currentPage->getTitle();

            $currentPageData = [
                'title' => $title,
                'content' => $htmlContent,
                'markdownContent' => $currentPage->getContent(),
                'pageCode' => $currentPage->getPageCode(),
            ];
        } elseif ($yamlTitle) {
            // Page defined in YAML but no DB entry yet
            $currentPageData = [
                'title' => $yamlTitle,
                'content' => array_fill_keys($acceptedLanguages, ''),
                'markdownContent' => array_fill_keys($acceptedLanguages, ''),
                'pageCode' => $actualPageCode,
            ];
        }

        // Get breadcrumb path
        $breadcrumbPath = $hierarchyService->getBreadcrumbPath($actualPageCode, $code);

        // Check if we should display content in a specific language (after edit)
        $contentLanguage = $request->getSession()->get('content_language');
        if ($contentLanguage) {
            // Clear it so it's only used once
            $request->getSession()->remove('content_language');
        }

        return $this->render('pages/journalPages.html.twig', [
            'review' => $review,
            'code' => $code,
            'pages' => $organizedPages,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
            'currentPage' => $pageCode,
            'currentPageData' => $currentPageData,
            'breadcrumbPath' => $breadcrumbPath,
            'editMode' => false,
            'contentLanguage' => $contentLanguage,
        ]);
    }
}
