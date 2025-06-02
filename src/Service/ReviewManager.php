<?php

namespace App\Service;

use App\Constants\ReviewConstants;
use App\Repository\ReviewRepository;

class ReviewManager
{
    public function __construct(
        private ReviewRepository $reviewRepository
    )
    {
    }

    /**
     * Récupère les reviews actives avec nouveau front pour l'affichage public
     */
    public function getActiveReviewsForDisplay(): array
    {
        // Utilise la méthode du repository
        $reviews = $this->reviewRepository->findActiveNewFrontReviews();

        // Ajouter la logique métier ici
        return $this->ReviewsData($reviews);
    }

    /**
     * Enrichit les données des reviews avec URLs et logos
     */
    private function ReviewsData(array $reviews): array
    {
        return array_map(function($review) {
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
        }, $reviews);
    }

    /**
     * Génère l'URL d'une review
     * Format: https://code.episciences.org
     */
    private function generateReviewUrl(string $code): string
    {
        return sprintf('https://%s.%s', $code, ReviewConstants::DOMAIN);
    }

    /**
     * Génère le chemin du logo d'une review
     */
    private function generateReviewLogo(string $code): string
    {
        return sprintf(ReviewConstants::LOGO_PATH_TEMPLATE, $code, ReviewConstants::DOMAIN, $code);
    }

}