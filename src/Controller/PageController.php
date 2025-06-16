<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/journal/{code}', name: 'app_page_index', requirements: ['code' => '[\w\-]+'])]
    public function index(string $code, ReviewRepository $reviewRepository): Response
    {
        // Récupérer la review par son code
        $review = $reviewRepository->findOneBy(['code' => $code]);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        return $this->render('page/index.html.twig', [
            'review' => $review
        ]);
    }


}