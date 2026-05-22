<?php

namespace App\Controller;


use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\JournalSettingService;
use App\Service\MarkdownService;
use App\Service\ReviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NewsController extends AbstractController
{
    private const NEWS_PER_PAGE = 20;
    private const NEWS_CONTENT_MAX_LENGTH = 5000;

    /**
     * Get plain text length from HTML content (strips tags)
     */
    private function getPlainTextLength(string $content): int
    {
        $plainText = strip_tags($content);
        $plainText = html_entity_decode($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return mb_strlen($plainText);
    }

    /**
     * Validate content length for all languages
     * @return bool True if valid, false if any content exceeds limit
     */
    private function validateContentLength(array $translations): bool
    {
        foreach ($translations as $lang => $data) {
            if (!empty($data['content'])) {
                $length = $this->getPlainTextLength($data['content']);
                if ($length > self::NEWS_CONTENT_MAX_LENGTH) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Convert news list with Markdown content to include HTML content
     */
    private function convertNewsListToHtml(array $newsList, MarkdownService $markdownService): array
    {
        $newsListWithHtml = [];
        foreach ($newsList as $news) {
            $content = $news->getContent() ?? [];
            $newsListWithHtml[] = [
                'id' => $news->getId(),
                'title' => $news->getTitle(),
                'content' => $content,
                'htmlContent' => $markdownService->convertContentArray($content),
                'link' => $news->getLink(),
                'visibility' => $news->getVisibility(),
                'dateCreation' => $news->getDateCreation(),
            ];
        }
        return $newsListWithHtml;
    }
    #[Route('/journal/{code}/news', name: 'app_news_show', methods: ['GET'])]
    public function index(
        string $code,
        ReviewManager $reviewManager,
        NewsRepository $newsRepository,
        PaginatorInterface $paginator,
        Request $request,
        Security $security,
        JournalSettingService $settingService,
        MarkdownService $markdownService
    ): Response {
        // Get the logged-in user (via CAS)
        $user = $security->getUser();
        $uid = $user?->getUid();

        // Get the review by its code
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        // Check if user has permission to view this specific review
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate(
            $newsRepository->queryByRvcode($code),
            $page,
            self::NEWS_PER_PAGE
        );
        $newsListWithHtml = $this->convertNewsListToHtml($pagination->getItems(), $markdownService);
        $pagination->setItems($newsListWithHtml);

        // Get accepted languages for this journal
        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];

        $defaultLanguage = $setting['languages']['default'] ?? 'en';

        return $this->render('news/journalNews.html.twig', [
            'review' => $review,
            'code' => $code,
            'newsList' => $pagination,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
            'currentUser' => $user,
        ]);
    }

    #[Route('/journal/{code}/news/create', name: 'app_news_create', methods: ['GET', 'POST'])]
    public function create(
        string $code,
        Request $request,
        ReviewManager $reviewManager,
        NewsRepository $newsRepository,
        PaginatorInterface $paginator,
        Security $security,
        JournalSettingService $settingService,
        EntityManagerInterface $entityManager,
        MarkdownService $markdownService,
        TranslatorInterface $translator
    ): Response
    {
        // Get the logged-in user (via CAS)
        $user = $security->getUser();

        // Get the review by its code
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        // Check if user has permission to view this specific review
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        // Handle POST request - save the news
        if ($request->isMethod('POST')) {
            // Validate CSRF token
            $token = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('news-create', $token)) {
                throw $this->createAccessDeniedException('Invalid CSRF token');
            }

            $status = $request->request->get('status');
            $translations = $request->request->all('translations');

            // Validate content length (security check - JS validation can be bypassed)
            if (!$this->validateContentLength($translations)) {
                return $this->redirectToRoute('app_news_show', ['code' => $code]);
            }

            // Build multilingual arrays from translations
            $titles = [];
            $contents = [];
            $links = [];

            foreach ($translations as $lang => $data) {
                if (!empty($data['title'])) {
                    $titles[$lang] = $data['title'];
                }
                if (!empty($data['content'])) {
                    $contents[$lang] = $data['content'];
                }
                if (!empty($data['link'])) {
                    $links[$lang] = $data['link'];
                }
            }

            // Validate at least one title exists (security check - JS validation can be bypassed)
            if (empty($titles)) {
                return $this->redirectToRoute('app_news_show', ['code' => $code]);
            }

            // Create new News entity
            $news = new News();
            $news->setRvcode($code);
            $news->setCreator($user);
            $news->setTitle($titles);
            $news->setContent($contents);
            $news->setLink($links);
            $news->setVisibility($status === 'public' ? ['public'] : ['private']);
            $news->setDateCreation(new \DateTime());
            $news->setDateUpdated(new \DateTime());

            $entityManager->persist($news);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('news.flash.created'));

            return $this->redirectToRoute('app_news_show', ['code' => $code]);
        }

        // GET request - show the page with form
        $pagination = $paginator->paginate(
            $newsRepository->queryByRvcode($code),
            $request->query->getInt('page', 1),
            self::NEWS_PER_PAGE
        );
        $newsListWithHtml = $this->convertNewsListToHtml($pagination->getItems(), $markdownService);
        $pagination->setItems($newsListWithHtml);

        // Get accepted languages for this journal
        $setting = $settingService->getSettingArray($review['rvid']);
        $acceptedLanguages = $setting['languages']['accepted'] ?? ['en', 'fr'];
        $defaultLanguage = $setting['languages']['default'] ?? 'en';

        return $this->render('news/journalNews.html.twig', [
            'review' => $review,
            'code' => $code,
            'newsList' => $pagination,
            'acceptedLanguages' => $acceptedLanguages,
            'defaultLanguage' => $defaultLanguage,
            'currentUser' => $user,
            'showCreateForm' => true,
        ]);
    }

    #[Route('/journal/{code}/news/{id}/edit', name: 'app_news_edit', methods: ['POST'])]
    public function edit(
        string $code,
        int $id,
        ReviewManager $reviewManager,
        NewsRepository $newsRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): Response
    {
        // Get the journal
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        // Get the news
        $news = $newsRepository->find($id);

        if (!$news || $news->getRvcode() !== $code) {
            throw $this->createNotFoundException('News not found');
        }

        // Validate CSRF token
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('news-edit', $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        // Get form data
        $status = $request->request->get('status');
        $translations = $request->request->all('translations');

        // Validate content length (security check - JS validation can be bypassed)
        if (!$this->validateContentLength($translations)) {
            return $this->redirectToRoute('app_news_show', ['code' => $code]);
        }

        // Build multilingual arrays
        $titles = [];
        $contents = [];
        $links = [];

        foreach ($translations as $lang => $data) {
            if (!empty($data['title'])) {
                $titles[$lang] = $data['title'];
            }
            if (!empty($data['content'])) {
                $contents[$lang] = $data['content'];
            }
            if (!empty($data['link'])) {
                $links[$lang] = $data['link'];
            }
        }

        // Validate at least one title exists (security check - JS validation can be bypassed)
        if (empty($titles)) {
            return $this->redirectToRoute('app_news_show', ['code' => $code]);
        }

        // Update the News entity
        $news->setTitle($titles);
        $news->setContent($contents);
        $news->setLink($links);
        $news->setVisibility($status === 'public' ? ['public'] : ['private']);
        $news->setDateUpdated(new \DateTime());

        $entityManager->flush();

        $this->addFlash('success', $translator->trans('news.flash.updated'));

        return $this->redirectToRoute('app_news_show', ['code' => $code]);
        }

    #[Route('/journal/{code}/news/{id}/delete', name: 'app_news_delete', methods: ['POST'])]
    public function delete(
        string $code,
        int $id,
        Request $request,
        ReviewManager $reviewManager,
        NewsRepository $newsRepository,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): Response
    {
        // Get the journal
        $review = $reviewManager->getReviewByCode($code);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);

        // Get the news
        $news = $newsRepository->find($id);

        if (!$news || $news->getRvcode() !== $code) {
            throw $this->createNotFoundException('News not found');
        }

        // Validate CSRF token
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('news-delete', $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        // Delete the news
        $entityManager->remove($news);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('news.flash.deleted'));

        return $this->redirectToRoute('app_news_show', ['code' => $code]);
    }

}
