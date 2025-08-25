<?php

namespace App\Service;

use Parsedown;
use League\HTMLToMarkdown\HtmlConverter;

class MarkdownService
{
    private Parsedown $parsedown;
    private HtmlConverter $htmlConverter;

    public function __construct()
    {
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(false); // Allow HTML tags for basic styling
        $this->parsedown->setMarkupEscaped(false); // Don't escape HTML markup
        $this->htmlConverter = new HtmlConverter([
            'strip_tags' => false,  // Keep HTML tags that have no Markdown equivalent
            'preserve_comments' => false,
            'remove_nodes' => 'script iframe object embed span[style*="color"]',  // Remove dangerous tags and color spans only
            'hard_break' => true,
            'strip_placeholder_links' => true
        ]);
    }

    /**
     * Convert Markdown text to HTML
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
        return array_map([$this, 'toHtml'], $content);
    }

    /**
     * Convert html to markdown
     */
    public function toMarkdown(string $html): string
    {
        return $this->htmlConverter->convert($html);
    }

}