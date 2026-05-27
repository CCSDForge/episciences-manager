<?php

namespace App\Service;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownService
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $config = [
            'html_input' => 'allow',           // Allow HTML (for img tags with attributes)
            'allow_unsafe_links' => false,     // Security: block javascript: etc.
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->converter = new MarkdownConverter($environment);
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