<?php

namespace App\Controller;


use App\Entity\News;
use App\Repository\NewsRepository;
use App\Repository\PageRepository;
use App\Service\JournalSettingService;
use App\Service\MarkdownService;
use App\Service\ReviewManager;
use App\Service\PageHierarchyService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/journal/{code}/news/create', name: 'app_news_create', methods: ['GET', 'POST'])]
    public function create(string $code, Request $request, ReviewManager $reviewManager, NewsRepository $newsRepository, Security $security, JournalSettingService $settingService, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (via CAS)
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

            // Create new News entity
            $news = new News();
            $news->setRvcode($code);
            $news->setCreator($user);
            $news->setTitle($titles);
            $news->setContent($contents);
            $news->setLink($links);
            $news->setVisibility($status === 'public' ? ['public'] : []);
            $news->setDateCreation(new \DateTime());
            $news->setDateUpdated(new \DateTime());

            $entityManager->persist($news);
            $entityManager->flush();

            $this->addFlash('success', 'News created successfully');

            return $this->redirectToRoute('app_news_show', ['code' => $code]);
        }

        // GET request - show the page with form
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
            'showCreateForm' => true,
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
        JournalSettingService $settingService,
        EntityManagerInterface $entityManager
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

// Valider le token CSRF
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('news-edit', $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        // Récupérer les données
        $status = $request->request->get('status');
        $translations = $request->request->all('translations');

        // Construire les tableaux multilingues
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

        // Mettre à jour l'entité News
            $news->setTitle($titles);
            $news->setContent($contents);
            $news->setLink($links);
            $news->setVisibility($status === 'public' ? ['public'] : []);
            $news->setDateUpdated(new \DateTime());

            $entityManager->flush();

            $this->addFlash('success', 'News updated successfully');

            return $this->redirectToRoute('app_news_show', ['code' => $code]);
        }

    #[Route('/journal/{code}/news/{id}/delete', name: 'app_news_delete', methods: ['POST'])]
    public function delete(
        string $code,
        int $id,
        Request $request,
        ReviewManager $reviewManager,
        NewsRepository $newsRepository,
        EntityManagerInterface $entityManager
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

        // Valider le token CSRF
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('news-delete', $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        // Supprimer la news
        $entityManager->remove($news);
        $entityManager->flush();

        $this->addFlash('success', 'News deleted successfully');

        return $this->redirectToRoute('app_news_show', ['code' => $code]);
    }

}
