<?php

namespace App\Controller;

use App\Entity\Page;
use App\Repository\PageRepository;
use App\Repository\ReviewRepository;
use App\Service\ReviewManager;
use App\Service\MarkdownService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}