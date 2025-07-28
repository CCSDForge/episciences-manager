<?php

namespace App\Controller;

use App\Entity\Page;
use App\Repository\PageRepository;
use App\Repository\ReviewRepository;
use App\Service\ReviewManager;
use App\Service\MarkdownService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/journal/{code}/page/{pageTitle}', name: 'app_page_show')]
    public function showPage(string $code, string $pageTitle, PageRepository $pageRepository, MarkdownService $markdownService, Request $request): Response
    {
        $page = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $pageTitle
        ]);

        if (!$page) {
            throw $this->createNotFoundException('Page not found');
        }

        // Si c'est une requête AJAX, retourner du JSON
        if ($request->isXmlHttpRequest()) {
            // Convert markdown content to HTML
            $htmlContent = $markdownService->convertContentArray($page->getContent());
            
            return new JsonResponse([
                'title' => $page->getTitle(),
                'content' => $htmlContent,
                'pageCode' => $page->getPageCode()
            ]);
        }

        // Pour les accès directs, rediriger vers la page principale du journal
        return $this->redirectToRoute('app_journal_detail', [
            'code' => $code
        ], 301);
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
        $page = $pageRepository->findOneBy([
            'rvcode' => $code,
            'page_code' => $pageTitle
        ]);

        if (!$page) {
            return new JsonResponse(['success' => false, 'message' => 'Page not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['content']) || !isset($data['locale'])) {
            return new JsonResponse(['success' => false, 'message' => 'Missing content or locale'], 400);
        }

        $content = $data['content'];
        $locale = $data['locale'];
        $title = $data['title'] ?? null;
        
        // Debug: log what we received
        error_log('Received data: ' . json_encode($data));
        error_log('Title received: ' . ($title ?? 'null'));

        // Update the content for the specific locale
        $currentContent = $page->getContent() ?? [];
        $currentContent[$locale] = $content;
        $page->setContent($currentContent);
        
        // Update the title if provided
        if ($title !== null) {
            $currentTitle = $page->getTitle() ?? [];
            $currentTitle[$locale] = $title;
            $page->setTitle($currentTitle);
        }

        try {
            $entityManager->flush();
            
            // Convert the new content to HTML and return it
            $htmlContent = $markdownService->convertContentArray($page->getContent());
            
            return new JsonResponse([
                'success' => true, 
                'message' => 'Page updated successfully',
                'htmlContent' => $htmlContent[$locale] ?? '',
                'updatedTitle' => $title ? $page->getTitle()[$locale] ?? '' : null
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Error saving page: ' . $e->getMessage()], 500);
        }
    }
}