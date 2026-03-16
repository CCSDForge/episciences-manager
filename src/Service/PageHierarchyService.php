<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class PageHierarchyService
{
    private readonly array $config;

    public function __construct(
        private readonly string $projectDir
    ) {
        $configFile = $this->projectDir . '/config/pages_hierarchy.yaml';
        $this->config = Yaml::parseFile($configFile)['page_hierarchies'] ?? [];
    }

    public function organizePages(array $pages, string $rvcode): array
    {
        // Use journal-specific config, fallback to default config
        $journalConfig = $this->config[$rvcode]['main_pages'] ?? $this->config['default']['main_pages'] ?? [];

        // If no configuration found, return pages as-is
        if (empty($journalConfig)) {
            return ['main' => $pages, 'sub' => []];
        }

        $organized = ['main' => [], 'sub' => []];

        // Index pages by code for quick access
        $pagesByCode = [];
        foreach ($pages as $page) {
            $pagesByCode[$page->getPageCode()] = $page;
        }

        // Get all configured page codes and create empty pages for missing ones
        $allConfiguredCodes = $this->getAllConfiguredCodes($journalConfig);
        foreach ($allConfiguredCodes as $pageCode) {
            if (!isset($pagesByCode[$pageCode])) {
                $pagesByCode[$pageCode] = $this->createEmptyPage($pageCode, $rvcode);
            }
        }

        // Process according to configuration
        foreach ($journalConfig as $pageConfig) {
            $this->processPageConfig($pageConfig, $pagesByCode, $organized);
        }

        return $organized;
    }

    private function processPageConfig(array $pageConfig, array $pagesByCode, array &$organized, ?string $parentKey = null): void
    {
        // Handle container pages (without pagecode)
        if (isset($pageConfig['type']) && $pageConfig['type'] === 'container') {
            $containerKey = is_array($pageConfig['title']) ?
                ($pageConfig['title']['en'] ?? 'container') :
                ($pageConfig['title'] ?? 'container');

            // Collect children information while preserving order
            $childrenInfo = [];

            if (isset($pageConfig['children'])) {
                foreach ($pageConfig['children'] as $child) {
                    // Check if child is a nested container (array) or a simple page code (string)
                    if (is_array($child)) {
                        // Nested container - mark for recursive processing
                        $childrenInfo[] = ['type' => 'container', 'config' => $child];
                    } elseif (isset($pagesByCode[$child])) {
                        // Simple page code
                        $childrenInfo[] = ['type' => 'page', 'page' => $pagesByCode[$child]];
                    }
                }
            }

            // Only create container if it has at least one existing child or nested container
            if (!empty($childrenInfo) || $this->hasNestedContainersWithChildren($pageConfig, $pagesByCode)) {
                // Create a virtual page object for the container
                $containerPage = new \stdClass();
                $containerPage->title = $pageConfig['title'] ?? 'Container';
                $containerPage->type = 'container';

                // Add to parent container or main
                if ($parentKey !== null) {
                    // This is a nested container, add it to parent's sub-pages
                    if (!isset($organized['sub'][$parentKey])) {
                        $organized['sub'][$parentKey] = [];
                    }
                    $organized['sub'][$parentKey][] = $containerPage;
                } else {
                    // This is a top-level container, add to main
                    $organized['main'][] = $containerPage;
                }

                // Process children in the order they appear in the YAML
                if (!isset($organized['sub'][$containerKey])) {
                    $organized['sub'][$containerKey] = [];
                }

                foreach ($childrenInfo as $childInfo) {
                    if ($childInfo['type'] === 'container') {
                        // Process nested container recursively
                        $this->processPageConfig($childInfo['config'], $pagesByCode, $organized, $containerKey);
                    } else {
                        // Add simple page directly
                        $organized['sub'][$containerKey][] = $childInfo['page'];
                    }
                }
            }
        } else {
            // Handle normal pages with code
            $pageCode = $pageConfig['code'];

            // If page exists in database
            if (isset($pagesByCode[$pageCode])) {
                // Add to parent container or main
                if ($parentKey !== null) {
                    if (!isset($organized['sub'][$parentKey])) {
                        $organized['sub'][$parentKey] = [];
                    }
                    $organized['sub'][$parentKey][] = $pagesByCode[$pageCode];
                } else {
                    $organized['main'][] = $pagesByCode[$pageCode];
                }

                // Process children if any
                if (isset($pageConfig['children'])) {
                    foreach ($pageConfig['children'] as $childCode) {
                        if (isset($pagesByCode[$childCode])) {
                            if (!isset($organized['sub'][$pageCode])) {
                                $organized['sub'][$pageCode] = [];
                            }
                            $organized['sub'][$pageCode][] = $pagesByCode[$childCode];
                        }
                    }
                }
            }
        }
    }

    private function hasNestedContainersWithChildren(array $pageConfig, array $pagesByCode): bool
    {
        if (!isset($pageConfig['children'])) {
            return false;
        }

        foreach ($pageConfig['children'] as $child) {
            if (is_array($child) && isset($child['type']) && $child['type'] === 'container') {
                // Check if nested container has any existing children
                if (isset($child['children'])) {
                    foreach ($child['children'] as $nestedChild) {
                        if (is_string($nestedChild) && isset($pagesByCode[$nestedChild])) {
                            return true;
                        }
                    }
                }
                // Recursively check for deeper nesting
                if ($this->hasNestedContainersWithChildren($child, $pagesByCode)) {
                    return true;
                }
            }
        }

        return false;
    }


    private function getAllConfiguredCodes(array $config): array
    {
        $codes = [];
        foreach ($config as $pageConfig) {
            if ($pageConfig === null) {
                continue;
            }
            // Only add code if it's not a container and code is set
            if ((!isset($pageConfig['type']) || $pageConfig['type'] !== 'container') && !empty($pageConfig['code'])) {
                $codes[] = $pageConfig['code'];
            }
            if (isset($pageConfig['children'])) {
                $codes = array_merge($codes, $this->extractCodesFromChildren($pageConfig['children']));
            }
        }
        return $codes;
    }

    private function extractCodesFromChildren(array $children): array
    {
        $codes = [];
        foreach ($children as $child) {
            if ($child === null) {
                continue;
            }
            if (is_array($child)) {
                // Nested container - recursively extract codes
                if (isset($child['children'])) {
                    $codes = array_merge($codes, $this->extractCodesFromChildren($child['children']));
                }
            } else {
                // Simple page code
                $codes[] = $child;
            }
        }
        return $codes;
    }

    /**
     * Create an empty page placeholder for pages defined in config but not in DB.
     */
    private function createEmptyPage(string $pageCode, string $rvcode): object
    {
        $emptyPage = new \stdClass();
        $emptyPage->id = null;
        $emptyPage->uid = 0;
        $emptyPage->pageCode = $pageCode;
        $emptyPage->rvcode = $rvcode;
        $emptyPage->title = ['en' => $this->formatPageTitle($pageCode), 'fr' => $this->formatPageTitle($pageCode)];
        $emptyPage->content = ['en' => '', 'fr' => ''];
        $emptyPage->visibility = ['public'];
        $emptyPage->isEmpty = true;

        return $emptyPage;
    }

    /**
     * Format page code into a readable title (e.g., "about" → "about", "editorial-board" → "editorial board").
     */
    private function formatPageTitle(string $pageCode): string
    {
        return str_replace('-', ' ', $pageCode);
    }

    /**
     * Get the page hierarchy configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}