<?php

namespace App\Controller;


use App\Service\ReviewManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{

    #[Route('/journal', name: 'app_journal')]
    public function index(Request $request, ReviewManager $reviewManager,PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in');
        }

        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $activeJournalsCount = $reviewManager->getActiveReviewsCount();

        if (!empty($search)) {
            $reviews = $reviewManager->searchReviews($search);
            $pagination = null;
        } else {
            $result = $reviewManager->getAllReviewsForDisplayPaginated($paginator,$page,10);
                $reviews = $result['reviews'];
                $pagination = $result['pagination'];

        }

        //dd($reviews);

        //dd($user);
        return $this->render('review/journal.html.twig', [
            'reviews' => $reviews,
            'pagination' => $pagination,
            'search' => $search,
            'user' => $user,
            'current_page' => $page,
            'activeJournalsCount' => $activeJournalsCount,
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

        //dd([
            //'review_rvid' => $review['rvid'],
            //'user_roles' => $this->getUser()->getRolesDetails(),
            //'code' => $code,
            //'full_review' => $review
       // ]);

        // Check if user has permission to view this specific review
        $this->denyAccessUnlessGranted('REVIEW_VIEW', $review);


        return $this->render('review/journalDetails.html.twig', [
            'review' => $review,
            'code' => $code,
        ]);
    }

}
