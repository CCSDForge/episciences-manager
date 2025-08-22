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
        $this->parsedown->setSafeMode(false); // Allow HTML tags for colors and styling
        $this->parsedown->setMarkupEscaped(false); // Don't escape HTML markup
        $this->htmlConverter = new HtmlConverter([
            'strip_tags' => false,  // Keep HTML tags that have no Markdown equivalent
            'preserve_comments' => false,
            'remove_nodes' => 'script iframe object embed',  // Remove dangerous tags
            'hard_break' => true,
            'strip_placeholder_links' => true
        ]);
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

    /**
     * Convert html to markdown
     */
    public function toMarkdown(string $html): string
    {
        return $this->htmlConverter->convert($html);
    }

    /**
     * Convert an array of HTML content to Markdown
     * Maintains the same array structure but converts content values
     */
    public function convertContentArrayToMarkdown(array $htmlContent): array
    {
        $convertedContent = [];

        foreach ($htmlContent as $locale => $htmlText) {
            $convertedContent[$locale] = $this->toMarkdown($htmlText);
        }

        return $convertedContent;
    }
}