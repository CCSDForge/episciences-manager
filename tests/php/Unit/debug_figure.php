<?php

require_once 'vendor/autoload.php';

use League\HTMLToMarkdown\HtmlConverter;

$html = '<figure class="image"><img src="http://epimanager-dev.episciences.org/en/epijinfo/resources/test.png" alt="Test image"></figure>';

// Test direct conversion without custom converter
$converter = new HtmlConverter([
    'strip_tags' => false,
    'preserve_comments' => false,
    'hard_break' => true
]);

echo "Original HTML:\n" . $html . "\n\n";
echo "Default conversion:\n" . $converter->convert($html) . "\n\n";

// Test with a simple converter to understand the structure
$converter->getEnvironment()->addConverter(new class implements \League\HTMLToMarkdown\Converter\ConverterInterface {
    public function convert(\League\HTMLToMarkdown\ElementInterface $element): string
    {
        echo "=== FIGURE DEBUG ===\n";
        echo "TagName: " . var_export($element->getTagName(), true) . "\n";
        echo "getValue(): " . var_export($element->getValue(), true) . "\n";
        
        // Try to use another method to get content
        $innerHTML = '';
        try {
            $innerHTML = $element->getChildrenAsString();
            echo "getChildrenAsString(): " . var_export($innerHTML, true) . "\n";
        } catch (Exception $e) {
            echo "getChildrenAsString() failed: " . $e->getMessage() . "\n";
        }
        
        // Try to iterate through children
        echo "Children count: " . count($element->getChildren()) . "\n";
        foreach ($element->getChildren() as $i => $child) {
            echo "Child $i: tag=" . var_export($child->getTagName(), true) . 
                 ", value=" . var_export($child->getValue(), true) . "\n";
            
            // If it's an image
            if ($child->getTagName() === 'img') {
                echo "IMG attributes:\n";
                echo "  src: " . var_export($child->getAttribute('src'), true) . "\n";
                echo "  alt: " . var_export($child->getAttribute('alt'), true) . "\n";
            }
        }
        echo "=== END DEBUG ===\n";
        
        return '<!-- FIGURE PROCESSED -->';
    }

    public function getSupportedTags(): array { return ['figure']; }
});

echo "With debug converter:\n" . $converter->convert($html) . "\n";