<?php

namespace App\Service;

use League\CommonMark\GithubFlavoredMarkdownConverter;

class MarkdownService
{
    public function __construct()
    {
        $this->converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'allow',           // Allow raw HTML in Markdown
            'allow_unsafe_links' => false,     // Security: block javascript: etc.
        ]);
    }

    /**
     * Convert Markdown to HTML (for display)
     */
    public function toHtml(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }

    /**
     * Convert an array of Markdown content into HTML
     */
    public function convertContentArray(array $content): array
    {
        return array_map([$this, 'toHtml'], $content);
    }
}
