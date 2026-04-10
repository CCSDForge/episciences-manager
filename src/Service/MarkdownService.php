<?php

namespace App\Service;

use League\CommonMark\GithubFlavoredMarkdownConverter;

class MarkdownService
{
    private GithubFlavoredMarkdownConverter $converter;

    public function __construct()
    {
        $this->converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',           // Allow raw HTML in Markdown
            'allow_unsafe_links' => false,     // Security: block javascript: etc.
        ]);
    }

    /**
     * Convert Markdown to HTML (for display)
     */
    public function toHtml(?string $markdown): string
    {
        if ($markdown === null || trim($markdown) === '') {
            return '';
        }
        return $this->converter->convert($markdown)->getContent();
    }

    /**
     * Convert an array of Markdown content into HTML
     */
    public function convertContentArray(array $content): array
    {
        $out = [];
        foreach ($content as $locale => $markdown) {
            $out[$locale] = $this->toHtml(is_scalar($markdown) ? (string) $markdown : '');
        }
        return $out;
    }
}
