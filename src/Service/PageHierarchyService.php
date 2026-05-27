<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class PageHierarchyService
{
    /** @var array<string, mixed> */
    private readonly array $config;

    public function __construct(
        private readonly string $projectDir
    ) {
        $configFile = $this->projectDir . '/config/pages_hierarchy.yaml';
        $this->config = Yaml::parseFile($configFile)['page_hierarchies'] ?? [];
    }

    /**
     * @param array<int, object> $pages
     * @return array{main: list<object>, sub: array<string, list<object>>}
     */
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

    /**
     * @param array<string, mixed> $pageConfig
     * @param array<string, object> $pagesByCode
     * @param array<string, mixed> $organized
     */
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
                    if (is_array($child)) {
                        // Array: either a nested container or a page with code+title
                        if (isset($child['type']) && $child['type'] === 'container') {
                            // Nested container - mark for recursive processing
                            $childrenInfo[] = ['type' => 'container', 'config' => $child];
                        } elseif (isset($child['code'])) {
                            // Page with code + title from YAML
                            $pageCode = $child['code'];
                            if (isset($pagesByCode[$pageCode])) {
                                $page = $pagesByCode[$pageCode];
                                // Inject title from YAML into page
                                if (isset($child['title'])) {
                                    if (method_exists($page, 'setTitle')) {
                                        $page->setTitle($child['title']);
                                    } else {
                                        // stdClass (empty page)
                                        $page->title = $child['title'];
                                    }
                                }
                                $childrenInfo[] = ['type' => 'page', 'page' => $page];
                            }
                        }
                    } elseif (is_string($child) && isset($pagesByCode[$child])) {
                        // Simple string page code (backward compatibility)
                        $childrenInfo[] = ['type' => 'page', 'page' => $pagesByCode[$child]];
                    }
                }
            }

            // Only create container if it has at least one existing child or nested container
            if ($childrenInfo !== [] || $this->hasNestedContainersWithChildren($pageConfig, $pagesByCode)) {
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

    /**
     * @param array<string, mixed> $pageConfig
     * @param array<string, object> $pagesByCode
     */
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
                        // Check for simple string code
                        if (is_string($nestedChild) && isset($pagesByCode[$nestedChild])) {
                            return true;
                        }
                        // Check for page with code+title format
                        if (is_array($nestedChild) && isset($nestedChild['code']) && isset($pagesByCode[$nestedChild['code']])) {
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


    /**
     * @param array<int, array<string, mixed>|null> $config
     * @return array<int, string>
     */
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

    /**
     * @param array<int, mixed> $children
     * @return array<int, string>
     */
    private function extractCodesFromChildren(array $children): array
    {
        $codes = [];
        foreach ($children as $child) {
            if ($child === null) {
                continue;
            }
            if (is_array($child)) {
                // Array: either a page with code+title or a nested container
                if (isset($child['code'])) {
                    // Page with code + title
                    $codes[] = $child['code'];
                }
                if (isset($child['children'])) {
                    // Nested container - recursively extract codes
                    $codes = array_merge($codes, $this->extractCodesFromChildren($child['children']));
                }
            } elseif (is_string($child)) {
                // Simple page code (backward compatibility)
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
     * Get title for a page code from YAML config
     * @return array<string, string>|null Title array by locale or null if not found
     */
    public function getTitleForPageCode(string $pageCode, string $rvcode): ?array
    {
        $journalConfig = $this->config[$rvcode]['main_pages']
            ?? $this->config['default']['main_pages']
            ?? [];

        return $this->findTitleInConfig($pageCode, $journalConfig);
    }

    /**
     * @param array<int, array<string, mixed>> $config
     * @return array<string, string>|null
     */
    private function findTitleInConfig(string $pageCode, array $config): ?array
    {
        foreach ($config as $item) {
            // Check if this item has the code we're looking for
            if (isset($item['code']) && $item['code'] === $pageCode && isset($item['title'])) {
                return $item['title'];
            }
            // Check children
            if (isset($item['children'])) {
                $result = $this->findTitleInConfig($pageCode, $item['children']);
                if ($result !== null) {
                    return $result;
                }
            }
        }
        return null;
    }

    /**
     * Get the page hierarchy configuration.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get the breadcrumb path for a page code
     * @return array<int, array<string, mixed>> Array of breadcrumb items
     */
    public function getBreadcrumbPath(string $pageCode, string $rvcode): array
    {
        $journalConfig = $this->config[$rvcode]['main_pages']
            ?? $this->config['default']['main_pages']
            ?? [];

        $path = [];
        $this->findPathToPage($pageCode, $journalConfig, $path);

        return $path;
    }

    /**
     * Recursively find the path to a page
     * @param array<int, array<string, mixed>> $config
     * @param array<int, array<string, mixed>> &$path
     */
    private function findPathToPage(string $targetPageCode, array $config, array &$path): bool
    {
        foreach ($config as $item) {
            // Check if this is the target page
            if (isset($item['code']) && $item['code'] === $targetPageCode) {
                return true; // Found it, don't add to path (current page is not in breadcrumb path)
            }

            // Check if this is a container
            $isContainer = isset($item['type']) && $item['type'] === 'container';

            // Check children
            if (isset($item['children'])) {
                // Temporarily add this item to path
                $breadcrumbItem = [
                    'type' => $isContainer ? 'container' : 'page',
                    'title' => $item['title'] ?? ['en' => 'Unknown', 'fr' => 'Inconnu'],
                ];
                if (!$isContainer && isset($item['code'])) {
                    $breadcrumbItem['code'] = $item['code'];
                }

                $path[] = $breadcrumbItem;

                // Search in children
                if ($this->findPathToPage($targetPageCode, $item['children'], $path)) {
                    return true; // Found in children, keep this item in path
                }

                // Not found in children, remove from path
                array_pop($path);
            }
        }

        return false;
    }
}
