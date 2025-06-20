<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use App\Service\ReviewManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{

    #[Route('/journal', name: 'app_journal')]
    public function index(ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->findAll();

        //dd($reviews);

        return $this->render('review/journal.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/journal/{code}', name: 'app_journal_detail', requirements: ['code' => '[\w\-]+'])]
    public function getJournal(string $code, ReviewManager $reviewManager): Response
    {
        // Récupérer la review par son code
        $review = $reviewManager->getReviewByCode($code);

        //dd($review);

        if (!$review) {
            throw $this->createNotFoundException('Review not found');
        }

        return $this->render('review/journalDetails.html.twig', [
            'review' => $review,
            'code' => $code,
        ]);
    }
}
