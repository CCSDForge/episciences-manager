<?php

namespace App\Service;

use App\Constants\ReviewConstants;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;

class ReviewManager
{
    public function __construct(
        private ReviewRepository $reviewRepository
    )
    {
    }

    /**
     * Retrieves active reviews with pagination
     */
    public function getActiveReviewsForDisplayPaginated(PaginatorInterface $paginator, int $page, int $limit = 8 )
    {
        // Récupérer le QueryBuilder
        $query = $this->reviewRepository->findActiveNewFrontReviews();

        // Utilise la méthode du repository
        $paginatedReviews = $paginator->paginate($query, $page, $limit);

        $reviewItems = [];
        foreach ($paginatedReviews->getItems() as $review) {
            $reviewItems[] = $this->SingleReview($review);
        }

        $paginatedReviews->setItems($reviewItems);

        return $paginatedReviews;
    }

    /**
     * Enriches review data with URLs and logos
     */
    private function SingleReview(array $review): array
    {
        return [
            'rvid' => $review['rvid'],
            'code' => $review['code'],
            'status' => $review['status'],
            'name' => $review['name'],
            'is_new_front_switched' => $review['is_new_front_switched'],
            // Logique métier : génération URL et logo
            'url' => $this->generateReviewUrl($review['code']),
            'logo' => $this->generateReviewLogo($review['code']),
        ];
    }


    /**
     * Generates the URL of a review
     * Format: https://code.episciences.org
     */
    private function generateReviewUrl(string $code): string
    {
        return sprintf('https://%s.%s', $code, ReviewConstants::DOMAIN);
    }

    /**
     * Generates the logo path of a review
     */
    private function generateReviewLogo(string $code): string
    {
        return sprintf(ReviewConstants::LOGO_PATH_TEMPLATE, $code, ReviewConstants::DOMAIN, $code);
    }

    /**
     * Retrieves all reviews for display
     */
    public function getAllReviewsForDisplay(): array
    {
        $reviews = $this->reviewRepository->findAllForList();
        $reviewItems = [];
        foreach ($reviews as $review) {
            $reviewItems[] = $this->SingleReview($review);
        }
        return $reviewItems;
    }


    /**
     * Get single review by code with full logo support
     */
    public function getReviewByCode(string $code): ?array
    {
        if (empty(trim($code))) {
            return null;
        }

        $review = $this->reviewRepository->findOneBy(['code' => $code]);
        if (!$review) {
            return null;
        }

        $reviewCode = $review->getCode();

        return [
            'rvid' => $review->getRvid(),
            'code' => $reviewCode,
            'status' => $review->getStatus(),
            'name' => $review->getName(),
            'is_new_front_switched' => $review->isNewFrontSwitched(),
            'url' => $this->generateReviewUrl($reviewCode),
            'logo' => $this->generateReviewLogo($reviewCode),
        ];
    }
}