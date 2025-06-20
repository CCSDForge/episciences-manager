<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use App\Service\ReviewManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{

    #[Route('/journal', name: 'app_journal')]
    public function index(Request $request, ReviewManager $reviewManager): Response
    {
        $search = $request->query->get('search', '');

        if (!empty($search)) {
            $reviews = $reviewManager->searchReviews($search);
        } else {
            $reviews = $reviewManager->getAllReviews();
        }

        //dd($reviews);

        return $this->render('review/journal.html.twig', [
            'reviews' => $reviews,
            'search' => $search,
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
