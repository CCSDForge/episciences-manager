<?php

namespace App\Service;

use Parsedown;

class MarkdownService
{
    private Parsedown $parsedown;

    public function __construct()
    {
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(true); // Enable XSS protection
    }

    /**
     * Convert markdown text to HTML
     */
    public function toHtml(string $markdown): string
    {
        return $this->parsedown->text($markdown);
    }

    /**
     * Convert an array of markdown content to HTML
     * Maintains the same array structure but converts content values
     */
    public function convertContentArray(array $content): array
    {
        $convertedContent = [];
        
        foreach ($content as $locale => $markdownText) {
            $convertedContent[$locale] = $this->toHtml($markdownText);
        }
        
        return $convertedContent;
    }
}