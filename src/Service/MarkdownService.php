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
        
        // Converter for <figure> - preserve HTML to maintain styling and dimensions
        $this->htmlConverter->getEnvironment()->addConverter(new class implements \League\HTMLToMarkdown\Converter\ConverterInterface {
            public function convert(\League\HTMLToMarkdown\ElementInterface $element): string
            {
                // Preserve the entire figure as HTML to maintain all styling
                // This includes width, height, alignment, and other CSS properties
                // Get all attributes from the figure element
                $attributes = [];
                if ($element->getAttribute('class') !== '' && $element->getAttribute('class') !== '0') {
                    $attributes[] = 'class="' . htmlspecialchars($element->getAttribute('class')) . '"';
                }
                if ($element->getAttribute('style') !== '' && $element->getAttribute('style') !== '0') {
                    $attributes[] = 'style="' . htmlspecialchars($element->getAttribute('style')) . '"';
                }
                
                $attributeString = $attributes !== [] ? ' ' . implode(' ', $attributes) : '';
                
                // Get the inner content - this might be processed or raw HTML
                $innerContent = $element->getValue();
                
                // If inner content looks like markdown (contains ![...]), 
                // we need to get the original HTML instead
                if (preg_match('/!\[([^\]]*)\]\(([^)]+)\)/', $innerContent)) {
                    // The content was processed to markdown, but we want to preserve original HTML
                    // We'll need to reconstruct or use a different approach
                    // For now, let's preserve what we have as HTML
                    $figureHtml = '<figure' . $attributeString . '>' . $innerContent . '</figure>';
                } else {
                    // Content is still HTML, preserve it
                    $figureHtml = '<figure' . $attributeString . '>' . $innerContent . '</figure>';
                }
                
                return $figureHtml . "\n\n";
            }

            public function getSupportedTags(): array { return ['figure']; }
        });

        // Converter for standalone <img> tags (not inside figures)
        $this->htmlConverter->getEnvironment()->addConverter(new class implements \League\HTMLToMarkdown\Converter\ConverterInterface {
            public function convert(\League\HTMLToMarkdown\ElementInterface $element): string
            {
                // Check if this img is inside a figure - if so, skip it (let figure converter handle it)
                $parent = $element->getParent();
                if ($parent && strtolower($parent->getTagName()) === 'figure') {
                    // Return the original HTML to be preserved by the figure converter
                    $attributes = [];
                    if ($element->getAttribute('src') !== '' && $element->getAttribute('src') !== '0') {
                        $attributes[] = 'src="' . htmlspecialchars($element->getAttribute('src')) . '"';
                    }
                    if ($element->getAttribute('alt') !== '' && $element->getAttribute('alt') !== '0') {
                        $attributes[] = 'alt="' . htmlspecialchars($element->getAttribute('alt')) . '"';
                    }
                    if ($element->getAttribute('style') !== '' && $element->getAttribute('style') !== '0') {
                        $attributes[] = 'style="' . htmlspecialchars($element->getAttribute('style')) . '"';
                    }
                    if ($element->getAttribute('width') !== '' && $element->getAttribute('width') !== '0') {
                        $attributes[] = 'width="' . htmlspecialchars($element->getAttribute('width')) . '"';
                    }
                    if ($element->getAttribute('height') !== '' && $element->getAttribute('height') !== '0') {
                        $attributes[] = 'height="' . htmlspecialchars($element->getAttribute('height')) . '"';
                    }
                    
                    return '<img ' . implode(' ', $attributes) . '>';
                }
                
                // Standalone image - check if it has sizing attributes/styles
                $src = trim($element->getAttribute('src'));
                if ($src === '') {
                    return '';
                }

                $alt = $element->getAttribute('alt');
                if ($alt === '') {
                    // fallback: use filename if alt is empty
                    $path = parse_url($src, PHP_URL_PATH) ?? '';
                    $basename = $path ? basename($path) : '';
                    $alt = $basename ?: 'image';
                }

                // Check if image has resizing attributes or is styled (from CKEditor)
                $hasResizing = false;
                $class = $element->getAttribute('class');
                $style = $element->getAttribute('style');
                $width = $element->getAttribute('width');
                $height = $element->getAttribute('height');
                
                // If image has sizing info (class image_resized, width/height attrs, or sizing styles)
                if (strpos($class, 'image_resized') !== false || 
                    $width !== '' && $width !== '0' || $height !== '' && $height !== '0' || 
                    (strpos($style, 'width') !== false || strpos($style, 'aspect-ratio') !== false)) {
                    $hasResizing = true;
                }
                
                if ($hasResizing) {
                    // Preserve as HTML to maintain all sizing information
                    $attributes = [];
                    if ($class !== '' && $class !== '0') {
                        $attributes[] = 'class="' . htmlspecialchars($class) . '"';
                    }
                    if ($style !== '' && $style !== '0') {
                        $attributes[] = 'style="' . htmlspecialchars($style) . '"';
                    }
                    if ($width !== '' && $width !== '0') {
                        $attributes[] = 'width="' . htmlspecialchars($width) . '"';
                    }
                    if ($height !== '' && $height !== '0') {
                        $attributes[] = 'height="' . htmlspecialchars($height) . '"';
                    }
                    
                    $attributes[] = 'src="' . htmlspecialchars($src) . '"';
                    $attributes[] = 'alt="' . htmlspecialchars($alt) . '"';
                    
                    return '<img ' . implode(' ', $attributes) . '>';
                }

                // Simple image without sizing - convert to markdown
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
     *
     * @param array<string, string> $content
     * @return array<string, string>
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
     *
     * @param array<string, string> $htmlContent
     * @return array<string, string>
     */
    public function convertContentArrayToMarkdown(array $htmlContent): array
    {
        return array_map([$this, 'toMarkdown'], $htmlContent);
    }
}