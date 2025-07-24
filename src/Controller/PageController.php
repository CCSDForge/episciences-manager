<?php

namespace App\Controller;

use App\Entity\Page;
use App\Repository\PageRepository;
use App\Repository\ReviewRepository;
use App\Service\ReviewManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/journal/{code}/page/{pageTitle}', name: 'app_page_show')]
    public function showPage(string $code, string $pageTitle, PageRepository $pageRepository, Request $request): Response
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
            return new JsonResponse([
                'title' => $page->getTitle(),
                'content' => $page->getContent(),
                'pageCode' => $page->getPageCode()
            ]);
        }

        // Sinon, retourner la vue normale
        return $this->render('page/show.html.twig', [
            'page' => $page,
            'code' => $code,
        ]);
    }
}