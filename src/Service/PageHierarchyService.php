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
            $this->processPageConfig($pageConfig, $pagesByCode, $organized);
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

    private function processPageConfig(array $pageConfig, array $pagesByCode, array &$organized, ?string $parentKey = null): void
    {
        // Handle container pages (without pagecode)
        if (isset($pageConfig['type']) && $pageConfig['type'] === 'container') {
            $containerKey = is_array($pageConfig['title']) ?
                ($pageConfig['title']['en'] ?? 'container') :
                ($pageConfig['title'] ?? 'container');

            // First, process children before creating the container
            // This ensures nested containers and their children are processed first
            $existingChildren = [];
            $hasNestedContainers = false;

            if (isset($pageConfig['children'])) {
                foreach ($pageConfig['children'] as $child) {
                    // Check if child is a nested container (array) or a simple page code (string)
                    if (is_array($child)) {
                        // Nested container - process it recursively
                        // Pass $containerKey as the parent so nested container goes into sub[$containerKey]
                        $this->processPageConfig($child, $pagesByCode, $organized, $containerKey);
                        $hasNestedContainers = true;
                    } elseif (isset($pagesByCode[$child])) {
                        // Simple page code
                        $existingChildren[] = $pagesByCode[$child];
                    }
                }
            }

            // Only create container if it has at least one existing child or nested container
            if (!empty($existingChildren) || $hasNestedContainers || $this->hasNestedContainersWithChildren($pageConfig, $pagesByCode)) {
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

                // Add direct children (non-container pages) to this container's sub-pages
                if (!empty($existingChildren)) {
                    if (!isset($organized['sub'][$containerKey])) {
                        $organized['sub'][$containerKey] = [];
                    }
                    $organized['sub'][$containerKey] = array_merge(
                        $organized['sub'][$containerKey],
                        $existingChildren
                    );
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
            // Only add code if it's not a container
            if (!isset($pageConfig['type']) || $pageConfig['type'] !== 'container') {
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


}