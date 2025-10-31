<?php

namespace App\Twig;

use App\Controller\PageController;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PageExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('page_slug', [$this, 'getPageSlug']),
        ];
    }

    /**
     * Get the URL slug for a given page code
     * Converts database page codes to their URL aliases
     * Example: 'journal-acknowledgments' => 'acknowledgments'
     */
    public function getPageSlug(string $pageCode): string
    {
        return PageController::getPageSlug($pageCode);
    }
}