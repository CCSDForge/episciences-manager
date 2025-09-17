<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class PageHierarchyService
{
    private array $config;

    public function __construct(string $projectDir)
    {
        $configFile = $projectDir . '/config/pages_hierarchy.yaml';
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
            $codes[] = $pageConfig['code'];
            if (isset($pageConfig['children'])) {
                $codes = array_merge($codes, $pageConfig['children']);
            }
        }
        return $codes;
    }

}