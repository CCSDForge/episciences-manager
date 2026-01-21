<?php

namespace App\Service;

use App\Entity\Page;
use App\Repository\PageRepository;

readonly class ResourceUsageService
{
    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    /**
     * Find pages that use a specific resource file
     *
     * @param string $filename The resource filename to search for
     * @param string $rvcode The journal code
     * @return array Array of pages using the resource with their details
     */
    public function findResourceUsage(string $filename, string $rvcode): array
    {
        $usage = [];

        // Search in page content for references to the resource
        $pages = $this->pageRepository->findBy(['rvcode' => $rvcode]);

        foreach ($pages as $page) {
            $foundInContent = $this->searchInPageContent($page, $filename, $rvcode);

            if (!empty($foundInContent)) {
                $usage[] = [
                    'page_code' => $page->getPageCode(),
                    'title' => $page->getTitle(),
                    'locations' => $foundInContent
                ];
            }
        }

        return $usage;
    }

    /**
     * Search for resource references in page content
     *
     * @param Page $page
     * @param string $filename
     * @param string $rvcode
     * @return array
     */
    private function searchInPageContent(Page $page, string $filename, string $rvcode): array
    {
        $locations = [];
        $content = $page->getContent();

        // Security: Escape special regex characters in filename to prevent regex injection
        $escapedFilename = preg_quote($filename, '/');
        $escapedRvcode = preg_quote($rvcode, '/');

        // Search patterns for different ways resources can be referenced
        $patterns = [
            // Direct resource URLs: /journal_code/resources/filename
            "/{$escapedRvcode}\/resources\/{$escapedFilename}/i",
            // Relative resource URLs: resources/filename
            "/resources\/{$escapedFilename}/i",
            // HTML img src attributes
            "/src=[\"']{$escapedRvcode}\/resources\/{$escapedFilename}[\"']/i",
            "/src=[\"']resources\/{$escapedFilename}[\"']/i",
            // HTML href attributes for links
            "/href=[\"']{$escapedRvcode}\/resources\/{$escapedFilename}[\"']/i",
            "/href=[\"']resources\/{$escapedFilename}[\"']/i",
            // Markdown image syntax
            "/!\[.*?\]\([^)]*{$escapedFilename}[^)]*\)/i",
            // Markdown link syntax
            "/\[.*?\]\([^)]*{$escapedFilename}[^)]*\)/i",
            // Just the filename itself (loose match)
            "/{$escapedFilename}/i"
        ];

        foreach ($content as $locale => $contentData) {
            if (is_string($contentData)) {
                $htmlContent = $contentData;
            } elseif (is_array($contentData) && isset($contentData['html'])) {
                $htmlContent = $contentData['html'];
            } else {
                continue;
            }

            foreach ($patterns as $index => $pattern) {
                if (preg_match($pattern, $htmlContent, $matches)) {
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
     * Get a human-readable description of the pattern type
     *
     * @param int $patternIndex
     * @return string
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
     * Get a summary of resource usage
     *
     * @param string $filename
     * @param string $rvcode
     * @return array
     */
    public function getResourceUsageSummary(string $filename, string $rvcode): array
    {
        $usage = $this->findResourceUsage($filename, $rvcode);

        return [
            'inUse' => !empty($usage),
            'pageCount' => count($usage),
            'pages' => array_map(function($page) {
                return [
                    'page_code' => $page['page_code'],
                    'title' => $page['title'],
                    'locationCount' => count($page['locations'])
                ];
            }, $usage)
        ];
    }
}