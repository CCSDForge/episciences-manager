<?php

namespace App\Service;

use App\Constants\ReviewConstants;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ReviewManager
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * Retrieves active reviews with pagination
     *
     * @return PaginationInterface<int, array<string, mixed>>
     */
    public function getActiveReviewsForDisplayPaginated(PaginatorInterface $paginator, int $page, int $limit = 8): PaginationInterface
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
     *
     * @param array<string, mixed> $review
     * @return array<string, mixed>
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
     *
     * @return array<int, array<string, mixed>>
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
     *
     * @return array<string, mixed>|null
     */
    public function getReviewByCode(string $code): ?array
    {
        if (in_array(trim($code), ['', '0'], true)) {
            return null;
        }

        $review = $this->reviewRepository->findOneBy(['code' => $code]);
        if (!$review instanceof \App\Entity\Review) {
            return null;
        }

        $review->getCode();

        return [
            ...$this->convertEntityToArray($review),
            'url' => $this->generateReviewUrl($review->getCode()),
            'logo' => $this->generateReviewLogo($review->getCode()),
        ];
    }

    /**
     * Converts Review entity to array format
     *
     * @return array<string, mixed>
     */
    private function convertEntityToArray(Review $review): array
    {
        return [
            'rvid' => $review->getRvid(),
            'code' => $review->getCode(),
            'status' => $review->getStatus(),
            'name' => $review->getName(),
            'is_new_front_switched' => $review->isNewFrontSwitched(),
        ];
    }

    /**
     * Get all reviews as arrays
     *
     * @return array{reviews: array<int, array<string, mixed>>, pagination: PaginationInterface<int, Review>}
     */
    public function getAllReviewsForDisplayPaginated(PaginatorInterface $paginator, int $page, int $limit): array
    {
        $allReviews = $this->reviewRepository->findAll();
        $paginatedReviews = $paginator->paginate($allReviews, $page, $limit);

        $reviews = [];

        foreach ( $paginatedReviews ->getItems() as $review) {
            $reviewArray = $this->getReviewByCode($review->getCode());
            if ($reviewArray) {
                $reviews[] = $reviewArray;
            }
        }

        return [
            'reviews' => $reviews,
            'pagination' => $paginatedReviews
        ];
    }

    /**
     * Search reviews and return as arrays
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchReviews(string $search): array
    {
        if (in_array(trim($search), ['', '0'], true)) {
            return [];
        }

        $reviewBySearch = $this->reviewRepository->findByCodeOrName($search);
        $reviews = [];

        foreach ($reviewBySearch as $review) {
            $reviewArray = $this->getReviewByCode($review->getCode());
            if ($reviewArray) {
                $reviews[] = $reviewArray;
            }
        }

        return $reviews;
    }

    public function getActiveReviewsCount(): int
    {
        return $this->entityManager->getRepository(Review::class)
            ->count(['status' => 1]);
    }

    /**
     * Get reviews where user has a valid role (epiadmin, administrator, chief_editor, secretary)
     *
     * @return array{reviews: array<int, array<string, mixed>>, pagination: PaginationInterface<int, Review>}
     */
    public function getReviewsForUserPaginated(User $user, PaginatorInterface $paginator, int $page, int $limit): array
    {
        $allowedRoles = ['epiadmin', 'administrator', 'chief_editor', 'secretary'];

        // Get RVID list where user has a valid role
        $userRvids = [];
        foreach ($user->getRolesDetails() as $role) {
            if (in_array($role['ROLEID'], $allowedRoles, true)) {
                $userRvids[] = (int)$role['RVID'];
            }
        }

        // Remove duplicates
        $userRvids = array_unique($userRvids);

        if ($userRvids === []) {
            return [
                'reviews' => [],
                'pagination' => $paginator->paginate([], $page, $limit)
            ];
        }

        // Get reviews filtered by user's RVID
        $allReviews = $this->reviewRepository->findBy(['rvid' => $userRvids]);
        $paginatedReviews = $paginator->paginate($allReviews, $page, $limit);

        $reviews = [];
        foreach ($paginatedReviews->getItems() as $review) {
            $reviewArray = $this->getReviewByCode($review->getCode());
            if ($reviewArray) {
                $reviews[] = $reviewArray;
            }
        }

        return [
            'reviews' => $reviews,
            'pagination' => $paginatedReviews
        ];
    }

    /**
     * Search reviews filtered by user's roles
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchReviewsForUser(User $user, string $search): array
    {
        if (in_array(trim($search), ['', '0'], true)) {
            return [];
        }

        $allowedRoles = ['epiadmin', 'administrator', 'chief_editor', 'secretary'];

        // Get RVID list where user has a valid role
        $userRvids = [];
        foreach ($user->getRolesDetails() as $role) {
            if (in_array($role['ROLEID'], $allowedRoles, true)) {
                $userRvids[] = (int)$role['RVID'];
            }
        }

        if ($userRvids === []) {
            return [];
        }

        $reviewBySearch = $this->reviewRepository->findByCodeOrName($search);
        $reviews = [];

        foreach ($reviewBySearch as $review) {
            // Only include if user has role for this review
            if (in_array($review->getRvid(), $userRvids, true)) {
                $reviewArray = $this->getReviewByCode($review->getCode());
                if ($reviewArray) {
                    $reviews[] = $reviewArray;
                }
            }
        }

        return $reviews;
    }
}