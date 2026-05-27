<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HtmlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('extract_first_image', [$this, 'extractFirstImage']),
            new TwigFilter('decode_entities', [$this, 'decodeEntities']),
        ];
    }

    /**
     * Extract the first image URL from HTML content
     * Returns null if no image is found
     */
    public function extractFirstImage(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return null;
        }

        // Match <img> tag and extract src attribute
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Decode HTML entities in a string
     */
    public function decodeEntities(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}