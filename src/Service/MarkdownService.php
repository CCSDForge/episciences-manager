<?php

namespace App\Service;

use League\HTMLToMarkdown\HtmlConverter;
use Parsedown;
use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

class MarkdownService
{
    private Parsedown $parsedown;
    private HtmlConverter $htmlConverter;

    public function __construct()
    {
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(false);      // allow HTML tags
        $this->parsedown->setMarkupEscaped(false); // do not escape HTML

        $this->htmlConverter = new HtmlConverter([
            'strip_tags' => false,       // keep <figure>/<img> so custom converters can handle them
            'preserve_comments' => false,
            'hard_break' => true
        ]);

        // Strategy: Pre-process HTML to replace figures with placeholders, then post-process
        
        // Converter for <figure> - need to work with already processed children
        $this->htmlConverter->getEnvironment()->addConverter(new class implements \League\HTMLToMarkdown\Converter\ConverterInterface {
            public function convert(\League\HTMLToMarkdown\ElementInterface $element): string
            {
                $captionMd = '';
                
                // The figure's value is already processed (img converted to markdown)
                $processedContent = $element->getValue();
                
                // Look for markdown image pattern in the processed content
                if (preg_match('/!\[([^\]]*)\]\(([^)]+)\)/', $processedContent, $matches)) {
                    $alt = $matches[1];
                    $src = $matches[2];
                    $imgMd = '![' . $alt . '](' . $src . ')';
                    
                    // Extract figcaption if present in original HTML (need to use a different approach)
                    // For now, just return the image markdown
                    return $imgMd . "\n\n";
                }

                return $processedContent . "\n\n";
            }

            public function getSupportedTags(): array { return ['figure']; }
        });

        // Converter for standalone <img> tags
        $this->htmlConverter->getEnvironment()->addConverter(new class implements \League\HTMLToMarkdown\Converter\ConverterInterface {
            public function convert(\League\HTMLToMarkdown\ElementInterface $element): string
            {
                $src = trim($element->getAttribute('src') ?? '');
                if ($src === '') return '';

                $alt = $element->getAttribute('alt') ?? '';
                if ($alt === '') {
                    // fallback: use filename if alt is empty
                    $path = parse_url($src, PHP_URL_PATH) ?? '';
                    $basename = $path ? basename($path) : '';
                    $alt = $basename ?: 'image';
                }

                return '![' . $alt . '](' . $src . ')';
            }

            public function getSupportedTags(): array { return ['img']; }
        });
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
        return array_map([$this, 'toHtml'], $content);
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
        return array_map([$this, 'toMarkdown'], $htmlContent);
    }
}