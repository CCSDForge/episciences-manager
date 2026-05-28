<?php

namespace App\Service;

use App\Entity\Page;
use App\Entity\News;
use App\Repository\PageRepository;
use App\Repository\NewsRepository;

readonly class ResourceUsageService
{
    public function __construct(
        private PageRepository $pageRepository,
        private NewsRepository $newsRepository
    ) {
    }

    /**
     * Find pages that use a specific resource file
     * Uses optimized SQL query instead of loading all pages
     *
     * @param string $filename The resource filename to search for
     * @param string $rvcode The journal code
     * @return array<int, array{page_code: string|null, title: array<string, string>, locations: array<int, array<string, string>>}>
     */
    public function findResourceUsageInPages(string $filename, string $rvcode): array
    {
        $usage = [];

        // Use optimized SQL query - only loads pages that contain the filename
        $pages = $this->pageRepository->findByContentContaining($filename, $rvcode);

        foreach ($pages as $page) {
            $foundInContent = $this->searchInPageContent($page, $filename, $rvcode);

            if ($foundInContent !== []) {
                $usage[] = [
                    'page_code' => $page->getPageCode(),
                    'title' => $page->getTitle(),
                    'locations' => $foundInContent,
                    'type' => 'page'
                ];
            }
        }

        return $usage;
    }

    /**
     * Find news that use a specific resource file
     * Uses optimized SQL query instead of loading all news
     *
     * @param string $filename The resource filename to search for
     * @param string $rvcode The journal code
     * @return array<int, array{news_id: int|null, title: array<string, string>, locations: array<int, array<string, string>>, type: string}>
     */
    public function findResourceUsageInNews(string $filename, string $rvcode): array
    {
        $usage = [];

        // Use optimized SQL query - only loads news that contain the filename
        $newsList = $this->newsRepository->findByContentContaining($filename, $rvcode);

        foreach ($newsList as $news) {
            $foundInContent = $this->searchInNewsContent($news, $filename, $rvcode);

            if ($foundInContent !== []) {
                $usage[] = [
                    'news_id' => $news->getId(),
                    'title' => $news->getTitle(),
                    'locations' => $foundInContent,
                    'type' => 'news'
                ];
            }
        }

        return $usage;
    }

    /**
     * Get search patterns for resource references
     *
     * @return array<int, string>
     */
    private function getSearchPatterns(string $filename, string $rvcode): array
    {
        return [
            // Direct resource URLs: /journal_code/resources/filename
            "/{$rvcode}\/resources\/" . preg_quote($filename, '/') . "/i",
            // Relative resource URLs: resources/filename
            "/resources\/" . preg_quote($filename, '/') . "/i",
            // HTML img src attributes
            "/src=[\"'][^\"']*" . preg_quote($filename, '/') . "[\"']/i",
            // HTML href attributes for links
            "/href=[\"'][^\"']*" . preg_quote($filename, '/') . "[\"']/i",
            // Markdown image syntax
            "/!\[.*?\]\([^)]*" . preg_quote($filename, '/') . "[^)]*\)/i",
            // Markdown link syntax
            "/\[.*?\]\([^)]*" . preg_quote($filename, '/') . "[^)]*\)/i",
            // Just the filename itself (loose match)
            "/" . preg_quote($filename, '/') . "/i"
        ];
    }

    /**
     * Search for resource references in content array
     *
     * @param array<string, string>|null $content
     * @return array<int, array<string, string>>
     */
    private function searchInContent(?array $content, string $filename, string $rvcode): array
    {
        $locations = [];

        if ($content === null) {
            return $locations;
        }

        $patterns = $this->getSearchPatterns($filename, $rvcode);

        foreach ($content as $locale => $contentData) {
            foreach ($patterns as $index => $pattern) {
                if (preg_match($pattern, $contentData, $matches)) {
                    $patternType = $this->getPatternDescription($index);
                    $locations[] = [
                        'locale' => $locale,
                        'type' => $patternType,
                        'match' => $matches[0] ?? $filename
                    ];
                    break; // Only count one match per locale to avoid duplicates
                }
            }
        }

        return $locations;
    }

    /**
     * Search for resource references in page content
     *
     * @return array<int, array<string, string>>
     */
    private function searchInPageContent(Page $page, string $filename, string $rvcode): array
    {
        return $this->searchInContent($page->getContent(), $filename, $rvcode);
    }

    /**
     * Search for resource references in news content
     *
     * @return array<int, array<string, string>>
     */
    private function searchInNewsContent(News $news, string $filename, string $rvcode): array
    {
        return $this->searchInContent($news->getContent(), $filename, $rvcode);
    }

    /**
     * Get a human-readable description of the pattern type
     */
    private function getPatternDescription(int $patternIndex): string
    {
        $descriptions = [
            0 => 'Direct URL reference',
            1 => 'Relative URL reference',
            2 => 'HTML image src (absolute)',
            3 => 'HTML image src (relative)',
            4 => 'HTML link href (absolute)',
            5 => 'HTML link href (relative)',
            6 => 'Markdown image',
            7 => 'Markdown link',
            8 => 'Filename reference'
        ];

        return $descriptions[$patternIndex] ?? 'Unknown reference';
    }

    /**
     * Get a summary of resource usage (pages and news combined)
     *
     * @return array{inUse: bool, pageCount: int, newsCount: int, pages: array<int, array<string, mixed>>, news: array<int, array<string, mixed>>}
     */
    public function getResourceUsageSummary(string $filename, string $rvcode): array
    {
        $pageUsage = $this->findResourceUsageInPages($filename, $rvcode);
        $newsUsage = $this->findResourceUsageInNews($filename, $rvcode);

        $pagesFormatted = array_map(function($page) {
            return [
                'page_code' => $page['page_code'],
                'title' => $page['title'],
                'locationCount' => count($page['locations']),
                'type' => 'page'
            ];
        }, $pageUsage);

        $newsFormatted = array_map(function($news) {
            return [
                'news_id' => $news['news_id'],
                'title' => $news['title'],
                'locationCount' => count($news['locations']),
                'type' => 'news'
            ];
        }, $newsUsage);

        return [
            'inUse' => $pageUsage !== [] || $newsUsage !== [],
            'pageCount' => count($pageUsage),
            'newsCount' => count($newsUsage),
            'pages' => $pagesFormatted,
            'news' => $newsFormatted
        ];
    }
}