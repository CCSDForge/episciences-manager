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

        // Process according to configuration
        foreach ($journalConfig as $pageConfig) {
            // Handle container pages (without code)
            if (isset($pageConfig['type']) && $pageConfig['type'] === 'container') {
                // First, collect existing children
                $existingChildren = [];
                if (isset($pageConfig['children'])) {
                    foreach ($pageConfig['children'] as $childCode) {
                        if (isset($pagesByCode[$childCode])) {
                            $existingChildren[] = $pagesByCode[$childCode];
                        }
                    }
                }

                // Only create container if it has at least one existing child
                if (!empty($existingChildren)) {
                    // Create a virtual page object for the container
                    $containerPage = new \stdClass();
                    $containerPage->title = $pageConfig['title'] ?? 'Container';
                    $containerPage->type = 'container';
                    // No more default_child needed!

                    $organized['main'][] = $containerPage;

                    // Add existing children to sub-pages
                    $containerKey = is_array($pageConfig['title']) ?
                        ($pageConfig['title']['en'] ?? 'container') :
                        ($pageConfig['title'] ?? 'container');
                    $organized['sub'][$containerKey] = $existingChildren;
                }
                // If no existing children, container is not displayed at all
            } else {
                // Handle normal pages with code
                $pageCode = $pageConfig['code'];

                // If page exists in database
                if (isset($pagesByCode[$pageCode])) {
                    $organized['main'][] = $pagesByCode[$pageCode];

                    // Process children if any
                    if (isset($pageConfig['children'])) {
                        foreach ($pageConfig['children'] as $childCode) {
                            if (isset($pagesByCode[$childCode])) {
                                $organized['sub'][$pageCode][] = $pagesByCode[$childCode];
                            }
                        }
                    }
                }
            }
        }

        // Add unconfigured pages at the end (safety fallback)
        $configuredCodes = $this->getAllConfiguredCodes($journalConfig);
        foreach ($pages as $page) {
            if (!in_array($page->getPageCode(), $configuredCodes)) {
                $organized['main'][] = $page;
            }
        }

        return $organized;
    }


    private function getAllConfiguredCodes(array $config): array
    {
        $codes = [];
        foreach ($config as $pageConfig) {
            // Only add code if it's not a container
            if (!isset($pageConfig['type']) || $pageConfig['type'] !== 'container') {
                $codes[] = $pageConfig['code'];
            }
            if (isset($pageConfig['children'])) {
                $codes = array_merge($codes, $pageConfig['children']);
            }
        }
        return $codes;
    }


}